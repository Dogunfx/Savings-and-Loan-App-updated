<?php

namespace Modules\Savings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SavingsChargeTypesTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('savings_charge_types')->insert([
            [
                'name' => 'Savings Activation',
                'translated_name' => 'Savings Activation',
            ],
            [
                'name' => 'Specified Due Date',
                'translated_name' => 'Specified Due Date',
            ],
            [
                'name' => 'Withdrawal Fee',
                'translated_name' => 'Withdrawal Fee',
            ],
            [
                'name' => 'Annual Fee',
                'translated_name' => 'Annual Fee',
            ],
            [
                'name' => 'Monthly Fee',
                'translated_name' => 'Monthly Fee',
            ],
            [
                'name' => 'Inactivity Fee',
                'translated_name' => 'Inactivity Fee',
            ],
            [
                'name' => 'Quarterly Fee',
                'translated_name' => 'Quarterly Fee',
            ],
        ]);
    }
}
