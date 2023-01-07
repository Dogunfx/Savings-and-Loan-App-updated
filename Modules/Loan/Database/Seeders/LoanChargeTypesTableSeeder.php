<?php

namespace Modules\Loan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanChargeTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('loan_charge_types')->insert([
            [
                'name' => 'Disbursement',
                'translated_name' => 'Disbursement',
            ],
            [
                'name' => 'Specified Due Date',
                'translated_name' => 'Specified Due Date',
            ],
            [
                'name' => 'Installment Fees',
                'translated_name' => 'Installment Fees',
            ],
            [
                'name' => 'Overdue Installment Fee',
                'translated_name' => 'Overdue Installment Fee',
            ],
            [
                'name' => 'Disbursement - Paid With Repayment',
                'translated_name' => 'Disbursement - Paid With Repayment',
            ],
            [
                'name' => 'Loan Rescheduling Fee',
                'translated_name' => 'Loan Rescheduling Fee',
            ],
            [
                'name' => 'Overdue On Loan Maturity',
                'translated_name' => 'Overdue On Loan Maturity',
            ],
            [
                'name' => 'Last installment fee',
                'translated_name' => 'Last installment fee',
            ],
        ]);
    }
}
