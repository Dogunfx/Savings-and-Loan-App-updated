<?php

namespace Modules\Share\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShareChargeOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('share_charge_options')->insert([
            [
                'name' => 'Flat',
                'translated_name' => 'Flat',
            ],
            [
                'name' => 'Percentage of amount',
                'translated_name' => 'Percentage of amount',
            ],
        ]);
    }
}
