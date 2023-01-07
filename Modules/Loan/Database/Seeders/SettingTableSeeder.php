<?php

namespace Modules\Loan\Database\Seeders;

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
                'name' => 'Loan Reference Prefix',
                'setting_key' => 'loan.reference_prefix',
                'module' => 'Loan',
                'setting_value' => 'L',
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
                'name' => 'Loan Reference Format',
                'setting_key' => 'loan.reference_format',
                'module' => 'Loan',
                'setting_value' => 'YEAR/Sequence Number (SL/2014/001)',
                'category' => 'system',
                'type' => 'select',
                'options' => 'YEAR/Sequence Number (SL/2014/001),YEAR/MONTH/Sequence Number (SL/2014/08/001),Sequence Number,Random Number,Branch Product Sequence Number',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '1',
                'rules' => ''
            ],


        ]);

    }
}
