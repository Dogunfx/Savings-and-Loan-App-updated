<?php

namespace Modules\Share\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShareTransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('share_transaction_types')->insert([
            [
                'name' => 'Purchase',
                'translated_name' => 'Purchase',
            ],
            [
                'name' => 'Redeem',
                'translated_name' => 'Redeem',
            ],
            [
                'name' => 'Waive Charges',
                'translated_name' => 'Waive Charges',
            ],
            [
                'name' => 'Apply Charges',
                'translated_name' => 'Apply Charges',
            ],
            [
                'name' => 'Pay Charge',
                'translated_name' => 'Pay Charge',
            ],
        ]);
    }
}
