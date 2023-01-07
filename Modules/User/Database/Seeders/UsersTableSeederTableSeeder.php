<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;

class UsersTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $user = User::create([
            'branch_id' => 1,
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@webstudio.co.zw',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'api_token' => Str::random(60),
            'google2fa_secret' => null,
            'email_verified_at' => date("Y-m-d H:i:s")
        ]);
        $role = Role::findByName("admin");
        $user->assignRole($role);
    }
}
