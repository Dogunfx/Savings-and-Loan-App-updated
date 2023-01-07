<?php

namespace Modules\Loan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanChargeOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('loan_charge_options')->insert([
            [
                'name' => 'Flat',
                'translated_name' => 'Flat',
            ],
            [
                'name' => 'Principal due on installment',
                'translated_name' => 'Principal due on installment',
            ],
            [
                'name' => 'Principal + Interest due on installment',
                'translated_name' => 'Principal + Interest due on installment',
            ],
            [
                'name' => 'Interest due on installment',
                'translated_name' => 'Interest due on installment',
            ],
            [
                'name' => 'Total Outstanding Loan Principal',
                'translated_name' => 'Total Outstanding Loan Principal',
            ],
            [
                'name' => 'Percentage of Original Loan Principal per Installment',
                'translated_name' => 'Percentage of Original Loan Principal per Installment',
            ],
            [
                'name' => 'Original Loan Principal',
                'translated_name' => 'Original Loan Principal',
            ]
        ]);
    }
}
