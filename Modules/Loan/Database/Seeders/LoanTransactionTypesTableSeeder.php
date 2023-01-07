<?php

namespace Modules\Loan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanTransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('loan_transaction_types')->insert([
            [
                'name' => 'Disbursement',
                'translated_name' => 'Disbursement',
            ],
            [
                'name' => 'Repayment',
                'translated_name' => 'Repayment',
            ],
            [
                'name' => 'Contra',
                'translated_name' => 'Contra',
            ],
            [
                'name' => 'Waive Interest',
                'translated_name' => 'Waive Interest',
            ],
            [
                'name' => 'Repayment At Disbursement',
                'translated_name' => 'Repayment At Disbursement',
            ],
            [
                'name' => 'Write Off',
                'translated_name' => 'Write Off',
            ],
            [
                'name' => 'Marked for Rescheduling',
                'translated_name' => 'Marked for Rescheduling',
            ],
            [
                'name' => 'Recovery Repayment',
                'translated_name' => 'Recovery Repayment',
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
            ]
        ]);
    }
}
