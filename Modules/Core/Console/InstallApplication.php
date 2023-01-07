<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\Entities\Menu;
use Nwidart\Modules\Facades\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallApplication extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Installing application');
        if (config('app.installed')) {
            $this->warn('Application already installed. If not then set APP_INSTALLED=false in .env to proceed');
        }
        {
            //migrate
            $modules = Module::getOrdered();
            foreach ($modules as $module) {
                Artisan::call('module:migrate', ['module' => $module->getName(), '--force' => true]);
                Artisan::call('module:seed', ['module' => $module->getName(), '--force' => true]);
            }
            //setup permissions and menus
            $this->info('Setting permissions and menus');
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
            $path = base_path('.env');
            $env = file($path);
            $env = str_replace('APP_INSTALLED=false' , 'APP_INSTALLED=true', $env);
            file_put_contents($path, $env);
            $this->info('Application successfully installed');
        }
    }

}
