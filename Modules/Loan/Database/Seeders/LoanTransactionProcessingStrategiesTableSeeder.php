<?php

namespace Modules\Loan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanTransactionProcessingStrategiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('loan_transaction_processing_strategies')->insert([
            [
                'name' => 'Penalties, Fees, Interest, Principal order',
                'translated_name' => 'Penalties, Fees, Interest, Principal order',
            ],
            [
                'name' => 'Principal, Interest, Penalties, Fees Order',
                'translated_name' => 'Principal, Interest, Penalties, Fees Order',
            ],
            [
                'name' => 'Interest, Principal, Penalties, Fees Order',
                'translated_name' => 'Interest, Principal, Penalties, Fees Order',
            ],
        ]);
    }
}
