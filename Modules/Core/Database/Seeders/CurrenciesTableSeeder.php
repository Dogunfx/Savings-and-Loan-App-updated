<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('currencies')->insert([
            [

                'rate' => 1.00,
                'code' => 'USD',
                'name' => 'United States dollar',
                'symbol' => '$',
                'position' => 'left',
            ]
        ]);
    }
}
