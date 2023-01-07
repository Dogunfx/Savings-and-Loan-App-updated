<?php

namespace Modules\Communication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SettingsTableSeederTableSeeder extends Seeder
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
                'name' => 'SMS Enabled',
                'setting_key' => 'communication.sms_enabled',
                'module' => 'Communication',
                'setting_value' => 'no',
                'category' => 'sms',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Active SMS Gateway',
                'setting_key' => 'communication.active_sms_gateway',
                'module' => 'Communication',
                'setting_value' => '1',
                'category' => 'sms',
                'type' => 'select_db',
                'options' => 'sms_gateways',
                'class' => 'select2',
                'required' => '0',
                'db_columns' => 'id,name',
                'displayed' => '1',
                'rules' => ''
            ]
        ]);
    }
}
