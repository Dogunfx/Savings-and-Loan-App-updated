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

class ResetDatabase extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'database:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the database.';

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
        $this->info('Reseting database');
        Artisan::call('migrate:reset');
        $modules = Module::getOrdered();
        foreach ($modules as $module) {
            echo $module->getName() . '<br>';
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
            Menu::where('module', $module->name)->delete();
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
        $this->info('Reset successful');
    }


}
