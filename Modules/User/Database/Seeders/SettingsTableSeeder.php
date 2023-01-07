<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('settings')->insert([
            [
                'name' => 'Registration Enabled',
                'setting_key' => 'user.enable_registration',
                'module' => 'User',
                'setting_value' => 'no',
                'category' => 'system',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1'
            ],
            [
                'name' => 'Enable Google recaptcha',
                'setting_key' => 'user.enable_google_recaptcha',
                'module' => 'User',
                'setting_value' => 'no',
                'category' => 'system',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1'
            ],
            [
                'name' => 'Google recaptcha site key',
                'setting_key' => 'user.google_recaptcha_site_key',
                'module' => 'User',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1'
            ],
            [
                'name' => 'Google recaptcha secret key',
                'setting_key' => 'user.google_recaptcha_secret_key',
                'module' => 'User',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1'
            ],
        ]);
    }
}
