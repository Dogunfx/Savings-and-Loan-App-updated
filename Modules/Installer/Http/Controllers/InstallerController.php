<?php

namespace Modules\Installer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Menu;
use Nwidart\Modules\Facades\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InstallerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('key:generate');
        Artisan::call('view:clear');
        return theme_view('installer::index');
    }

    public function requirements()
    {
        $requirements = [
            'PHP Version (>= 7.2)' => version_compare(phpversion(), '7.2', '>='),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'BCMath PHP Extension' => extension_loaded('bcmath'),
            'Ctype PHP Extension' => extension_loaded('ctype'),
            'JSON PHP Extension' => extension_loaded('json'),
            'PDO Extension' => extension_loaded('PDO'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'GD Extension' => extension_loaded('gd'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'XML PHP Extension' => extension_loaded('xml')
        ];
        $next = true;
        foreach ($requirements as $key) {
            if ($key == false) {
                $next = false;
            }
        }
        return theme_view('installer::requirements', compact('requirements', 'next'));
    }

    public function permissions()
    {
        $permissions = [
            'public/uploads' => is_writable(public_path('uploads')),
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'storage/logs' => is_writable(storage_path('logs')),
            'storage' => is_writable(storage_path('')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env file' => is_writable(base_path('.env')),
        ];
        $next = true;
        foreach ($permissions as $key) {
            if ($key == false) {
                $next = false;
            }
        }
        return theme_view('installer::permissions', compact('permissions', 'next'));
    }

    public function database(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = array();
            $credentials["host"] = $request->host;
            $credentials["username"] = $request->username;
            $credentials["password"] = $request->password;
            $credentials["name"] = $request->name;
            $credentials["port"] = $request->port;
            $default = config('database.default');

            config([
                "database.connections.{$default}.host" => $credentials['host'],
                "database.connections.{$default}.database" => $credentials['name'],
                "database.connections.{$default}.username" => $credentials['username'],
                "database.connections.{$default}.password" => $credentials['password'],
                "database.connections.{$default}.port" => $credentials['port']
            ]);

            $path = base_path('.env');
            $env = file($path);

            $env = str_replace('DB_HOST=' . env('DB_HOST'), 'DB_HOST=' . $credentials['host'], $env);
            $env = str_replace('DB_DATABASE=' . env('DB_DATABASE'), 'DB_DATABASE=' . $credentials['name'], $env);
            $env = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME=' . $credentials['username'], $env);
            $env = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD=' . $credentials['password'], $env);
            $env = str_replace('DB_PORT=' . env('DB_PORT'), 'DB_PORT=' . $credentials['port'], $env);
            file_put_contents($path, $env);
            try {
                DB::statement("SHOW TABLES");
                //connection made,lets install database
                return redirect('install/installation');
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                Flash::warning(trans('installer::general.install_database_failed'));
                //copy(base_path('.env.example'), base_path('.env'));
                return redirect()->back()->with(["error" => trans('installer::general.install_database_failed')]);
            }

        }
        return theme_view('installer::database');
    }

    public function installation(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                //migrate
                $modules=Module::getOrdered();
                foreach ($modules as $module) {
                    echo $module->getName().'<br>';
                    Artisan::call('module:migrate', ['module'=>$module->getName(),'--force'=>true]);
                    Artisan::call('module:seed', ['module'=>$module->getName(),'--force'=>true]);
                }
                //setup permissions and menus
                foreach ($modules as $module) {
                    $permissions = config($module->getLowerName() . '.permissions');
                    if ($permissions) {
                        foreach ($permissions as $key) {
                            if (!Permission::where('name', $key['name'])->first()) {
                                Permission::create($key);
                            }
                        }
                    }
                    $admin = Role::findByName('admin');
                    $admin->syncPermissions(Permission::all());
                    //reconfigure menu
                    $menus = config($module->getLowerName() . '.menus');
                    Menu::where('module', $module->getName())->delete();
                    if ($menus) {
                        foreach ($menus as $menu) {
                            $m = new Menu();
                            $m->is_parent = $menu['is_parent'];
                            if ($menu['is_parent'] != 1) {
                                //find the parent
                                $parent = Menu::where('slug', $menu['parent_slug'])->first();
                                if (!empty($parent)) {
                                    $m->parent_id = $parent->id;
                                }
                            }
                            $m->parent_slug = $menu['parent_slug'];
                            $m->name = $menu['name'];
                            $m->slug = $menu['slug'];
                            $m->module = $menu['module'];
                            $m->url = $menu['url'];
                            $m->icon = $menu['icon'];
                            $m->menu_order = $menu['order'];
                            $m->permissions = $menu['permissions'];
                            $m->save();
                        }
                    }
                }
                file_put_contents(storage_path('installed'), 'Welcome to ULM');

                return redirect('install/complete');

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
                Flash::warning(trans('installer::general.install_error'));
                return redirect()->back();
            }
        }
        return theme_view('installer::installation');
    }

    public function complete()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        $path = base_path('.env');
        $env = file($path);
        $env = str_replace('APP_INSTALLED=' . config('app.installed'), 'APP_INSTALLED=true', $env);
        file_put_contents($path, $env);
        return theme_view('installer::complete');
    }
}
