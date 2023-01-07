<?php

namespace Modules\Share\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShareChargeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('share_charge_types')->insert([
            [
                'name' => 'Share Account Activation',
                'translated_name' => 'Share Account Activation',
            ],
            [
                'name' => 'Share Purchase',
                'translated_name' => 'Share Purchase',
            ],
            [
                'name' => 'Share Redeem',
                'translated_name' => 'Share Redeem',
            ],
        ]);
    }
}
