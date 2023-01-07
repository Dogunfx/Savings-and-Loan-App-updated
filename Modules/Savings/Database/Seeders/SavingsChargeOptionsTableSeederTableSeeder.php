<?php

namespace Modules\Savings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SavingsChargeOptionsTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('savings_charge_options')->insert([
            [
                'name' => 'Flat',
                'translated_name' => 'Flat',
            ],
            [
                'name' => 'Percentage of amount',
                'translated_name' => 'Percentage of amount',
            ],
            [
                'name' => 'Percentage of savings balance',
                'translated_name' => 'Percentage of savings balance',
            ],

        ]);
    }
}
