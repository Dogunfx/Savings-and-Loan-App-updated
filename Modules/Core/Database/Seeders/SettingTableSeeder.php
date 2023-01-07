<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
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
                'name' => 'Company Name',
                'setting_key' => 'core.company_name',
                'module' => 'Core',
                'setting_value' => 'Ultimate Loan Manager',
                'category' => 'general',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Company Address',
                'setting_key' => 'core.company_address',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'general',
                'type' => 'textarea',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Company Country',
                'setting_key' => 'core.company_country',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'general',
                'type' => 'select_db',
                'options' => 'countries',
                'class' => 'select2',
                'required' => '0',
                'db_columns' => 'id,name',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Timezone',
                'setting_key' => 'core.timezone',
                'module' => 'Core',
                'setting_value' => '1',
                'category' => 'general',
                'type' => 'select_db',
                'options' => 'timezones',
                'class' => 'select2',
                'required' => '1',
                'db_columns' => 'id,zone_name',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'System Version',
                'setting_key' => 'core.system_version',
                'module' => 'Core',
                'setting_value' => '3.0',
                'category' => 'update',
                'type' => 'info',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Company Email',
                'setting_key' => 'core.company_email',
                'module' => 'Core',
                'setting_value' => 'nonreply@company.com',
                'category' => 'general',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Company Logo',
                'setting_key' => 'core.company_logo',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'general',
                'type' => 'file',
                'options' => 'jpeg,jpg,bmp,png',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => 'nullable,file|mimes:jpeg,jpg,bmp,png'
            ],
            [
                'name' => 'Site Online',
                'setting_key' => 'core.site_online',
                'module' => 'Core',
                'setting_value' => 'yes',
                'category' => 'system',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Console Last Run',
                'setting_key' => 'core.console_last_run',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'info',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Update Url',
                'setting_key' => 'core.update_url',
                'module' => 'Core',
                'setting_value' => 'http://webstudio.co.zw/ulm/update',
                'category' => 'general',
                'type' => 'info',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
            [
                'name' => 'Auto Download Update',
                'setting_key' => 'core.auto_download_update',
                'module' => 'Core',
                'setting_value' => 'no',
                'category' => 'system',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Update last checked',
                'setting_key' => 'core.update_last_checked',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'info',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Extra Javascript',
                'setting_key' => 'core.extra_javascript',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'textarea',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Extra Styles',
                'setting_key' => 'core.extra_styles',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'textarea',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Demo Mode',
                'setting_key' => 'core.demo_mode',
                'module' => 'Core',
                'setting_value' => 'no',
                'category' => 'system',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Purchase Code',
                'setting_key' => 'core.purchase_code',
                'module' => 'Core',
                'setting_value' => '',
                'category' => 'system',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],
            [
                'name' => 'Active Theme',
                'setting_key' => 'core.active_theme',
                'module' => 'Core',
                'setting_value' => 'AdminLTE',
                'category' => 'system',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],

        ]);

    }
}
