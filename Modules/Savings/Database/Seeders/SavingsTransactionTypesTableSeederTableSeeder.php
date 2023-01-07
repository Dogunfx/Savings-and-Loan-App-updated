<?php

namespace Modules\Savings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SavingsTransactionTypesTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('savings_transaction_types')->insert([
            [
                'name' => 'Deposit',
                'translated_name' => 'Deposit',
            ],
            [
                'name' => 'Withdrawal',
                'translated_name' => 'Withdrawal',
            ],
            [
                'name' => 'Dividend',
                'translated_name' => 'Dividend',
            ],
            [
                'name' => 'Waive Interest',
                'translated_name' => 'Waive Interest',
            ],
            [
                'name' => 'Guarantee',
                'translated_name' => 'Guarantee',
            ],
            [
                'name' => 'Guarantee Restored',
                'translated_name' => 'Guarantee Restored',
            ],
            [
                'name' => 'Loan Repayment',
                'translated_name' => 'Loan Repayment',
            ],
            [
                'name' => 'Transfer',
                'translated_name' => 'Transfer',
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
                'name' => 'Apply Interest',
                'translated_name' => 'Apply Interest',
            ],
            [
                'name' => 'Pay Charge',
                'translated_name' => 'Pay Charge',
            ]
        ]);
    }
}
