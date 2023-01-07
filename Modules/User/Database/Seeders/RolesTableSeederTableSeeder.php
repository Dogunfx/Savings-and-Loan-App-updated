<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $admin = Role::create(['name' => 'admin', 'is_system' => 1]);
        //assign default permissions
        $admin->syncPermissions(Permission::all());
        $client = Role::create(['name' => 'client', 'is_system' => 1]);
    }
}
