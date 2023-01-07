<?php

namespace Modules\Communication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommunicationCampaignBusinessRulesTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('communication_campaign_business_rules')->insert([
            [
                'name' => 'Active Clients',
                'is_trigger' => 0,
                'description' => 'All clients with the status ‘Active’',
                'active' => 1,

            ],
            [
                'name' => 'Prospective Clients',
                'is_trigger' => 0,
                'description' => 'All clients with the status ‘Active’ who have never had a loan before',
                'active' => 1,
            ],
            [
                'name' => 'Active Loan Clients',
                'is_trigger' => 0,
                'description' => 'All clients with an outstanding loan',
                'active' => 1,
            ],
            [
                'name' => 'Loans in arrears',
                'is_trigger' => 0,
                'description' => 'All clients with an outstanding loan in arrears between X and Y days',
                'active' => 1,

            ],
            [
                'name' => 'Loans disbursed to clients',
                'is_trigger' => 0,
                'description' => 'All clients who have had a loan disbursed to them in the last X to Y days',
                'active' => 1,
            ],
            [
                'name' => 'Loan payments due',
                'is_trigger' => 0,
                'description' => 'All clients with an unpaid installment due on their loan between X and Y days',
                'active' => 1,
            ], [
                'name' => 'Dormant Prospects',
                'is_trigger' => 0,
                'description' => 'All individuals who have not yet received a loan but were also entered into the system more than 3 months',
                'active' => 0,

            ],
            [
                'name' => 'Loan Payments Due (Overdue Loans)',
                'is_trigger' => 0,
                'description' => 'Loan Payments Due between X to Y days for clients in arrears between X and Y days',
                'active' => 0,

            ],
            [
                'name' => 'Loan Payments Received (Active Loans)',
                'is_trigger' => 0,
                'description' => 'Payments received in the last X to Y days for any loan with the status Active (on-time)',
                'active' => 0,

            ],
            [
                'name' => 'Loan Payments Received (Overdue Loans) ',
                'is_trigger' => 0,
                'description' => 'Payments received in the last X to Y days for any loan with the status Overdue (arrears) between X and Y days',
                'active' => 0,

            ],
            [
                'name' => 'Happy Birthday',
                'is_trigger' => 0,
                'description' => 'This sends a message to all clients with the status Active on their Birthday',
                'active' => 0,
            ],
            [
                'name' => 'Loan Fully Repaid',
                'is_trigger' => 0,
                'description' => 'All loans that have been fully repaid (Closed or Overpaid) in the last X to Y days',
                'active' => 0,
            ],
            [
                'name' => 'Loans Outstanding after final instalment date',
                'is_trigger' => 0,
                'description' => 'All active loans (with an outstanding balance) between X to Y days after the final instalment date on their loan schedule',
                'active' => 0,
            ],
            [
                'name' => 'Past Loan Clients',
                'is_trigger' => 0,
                'description' => 'Past Loan Clients who have previously had a loan but do not currently have one and finished repaying their most recent loan in the last X to Y days.',
                'active' => 0,
            ],
            [
                'name' => 'Loan Submitted',
                'is_trigger' => 1,
                'description' => 'Loan and client data of submitted loan',
                'active' => 1,
            ],
            [
                'name' => 'Loan Rejected',
                'is_trigger' => 1,
                'description' => 'Loan and client data of rejected loan',
                'active' => 1,
            ],
            [
                'name' => 'Loan Approved',
                'is_trigger' => 1,
                'description' => 'Loan and client data of approved loan',
                'active' => 1,

            ],
            [
                'name' => 'Loan Disbursed',
                'is_trigger' => 1,
                'description' => 'Loan Disbursed',
                'active' => 1,
            ],
            [
                'name' => 'Loan Rescheduled',
                'is_trigger' => 1,
                'description' => 'Loan Rescheduled',
                'active' => 1,
            ],
            [
                'name' => 'Loan Closed',
                'is_trigger' => 1,
                'description' => 'Loan Closed',
                'active' => 1,
            ],

            [
                'name' => 'Loan Repayment',
                'is_trigger' => 1,
                'description' => 'Loan Repayment',
                'active' => 1,
            ],
            [
                'name' => 'Savings Submitted',
                'is_trigger' => 1,
                'description' => 'Savings and client data of submitted savings',
                'active' => 1,
            ],
            [
                'name' => 'Savings Rejected',
                'is_trigger' => 1,
                'description' => 'Savings and client data of rejected savings',
                'active' => 1,
            ],
            [
                'name' => 'Savings Approved',
                'is_trigger' => 1,
                'description' => 'Savings and client data of approved savings',
                'active' => 1,

            ],
            [
                'name' => 'Savings Activated',
                'is_trigger' => 1,
                'description' => 'Savings Activated',
                'active' => 1,
            ],
            [
                'name' => 'Savings Dormant',
                'is_trigger' => 1,
                'description' => 'Savings Dormant',
                'active' => 1,
            ],
            [
                'name' => 'Savings Inactive',
                'is_trigger' => 1,
                'description' => 'Savings Inactive',
                'active' => 1,
            ],
            [
                'name' => 'Savings Closed',
                'is_trigger' => 1,
                'description' => 'Savings Closed',
                'active' => 1,
            ],
            [
                'name' => 'Savings Deposit',
                'is_trigger' => 1,
                'description' => 'Savings Deposit',
                'active' => 1,
            ],
            [
                'name' => 'Savings Withdrawal',
                'is_trigger' => 1,
                'description' => 'Savings Withdrawal',
                'active' => 1,
            ]
        ]);
    }
}
