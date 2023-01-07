<?php

namespace Modules\Loan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanCreditChecksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::table('loan_credit_checks')->insert([
            [
                'name' => 'Client Written-Off Loans Check',
                'translated_name' => 'Client Written-Off Loans Check',
                'security_level' => 'block',
                'rating_type' => 'boolean',
                'general_error_msg' => 'The client has one or more written-off loans',
                'user_friendly_error_msg' => 'The client has one or more written-off loans',
                'general_warning_msg' => 'The client has one or more written-off loans',
                'user_friendly_warning_msg' => 'The client has one or more written-off loans',
                'general_success_msg' => 'The client has one or more written-off loans',
                'user_friendly_success_msg' => 'The client has one or more written-off loans',
            ],
            [
                'name' => 'Same Product Outstanding',
                'translated_name' => 'Same Product Outstanding',
                'security_level' => 'block',
                'rating_type' => 'boolean',
                'general_error_msg' => 'The client has an active loan for the same product',
                'user_friendly_error_msg' => 'The client has an active loan for the same product',
                'general_warning_msg' => 'The client has an active loan for the same product',
                'user_friendly_warning_msg' => 'The client has an active loan for the same product',
                'general_success_msg' => 'The client has an active loan for the same product',
                'user_friendly_success_msg' => 'The client has an active loan for the same product',
            ],
            [
                'name' => 'Client Arrears',
                'translated_name' => 'Client Arrears',
                'security_level' => 'block',
                'rating_type' => 'boolean',
                'general_error_msg' => 'Client has arrears on existing loans',
                'user_friendly_error_msg' => 'Client has arrears on existing loans',
                'general_warning_msg' => 'Client has arrears on existing loans',
                'user_friendly_warning_msg' => 'Client has arrears on existing loans',
                'general_success_msg' => 'Client has arrears on existing loans',
                'user_friendly_success_msg' => 'Client has arrears on existing loans',
            ],
            [
                'name' => 'Outstanding Loan Balance',
                'translated_name' => 'Outstanding Loan Balance',
                'security_level' => 'block',
                'rating_type' => 'boolean',
                'general_error_msg' => 'Client has outstanding balance on existing loans',
                'user_friendly_error_msg' => 'Client has outstanding balance on existing loans',
                'general_warning_msg' => 'Client has outstanding balance on existing loans',
                'user_friendly_warning_msg' => 'Client has outstanding balance on existing loans',
                'general_success_msg' => 'Client has outstanding balance on existing loans',
                'user_friendly_success_msg' => 'Client has outstanding balance on existing loans',
            ],
            [
                'name' => 'Rejected and withdrawn loans',
                'translated_name' => 'Rejected and withdrawn loans',
                'security_level' => 'block',
                'rating_type' => 'boolean',
                'general_error_msg' => 'This client has had one or more rejected or withdrawn loans',
                'user_friendly_error_msg' => 'This client has had one or more rejected or withdrawn loans',
                'general_warning_msg' => 'This client has had one or more rejected or withdrawn loans',
                'user_friendly_warning_msg' => 'This client has had one or more rejected or withdrawn loans',
                'general_success_msg' => 'This client has had one or more rejected or withdrawn loans',
                'user_friendly_success_msg' => 'This client has had one or more rejected or withdrawn loans',
            ],
            [
                'name' => 'Total collateral items value',
                'translated_name' => 'Total collateral items value',
                'security_level' => 'block',
                'rating_type' => 'boolean',
                'general_error_msg' => 'The total value of collateral items for this loan is less than the principal loanamount',
                'user_friendly_error_msg' => 'The total value of collateral items for this loan is less than the principal loanamount',
                'general_warning_msg' => 'The total value of collateral items for this loan is less than the principal loanamount',
                'user_friendly_warning_msg' => 'The total value of collateral items for this loan is less than the principal loanamount',
                'general_success_msg' => 'The total value of collateral items for this loan is less than the principal loanamount',
                'user_friendly_success_msg' => 'The total value of collateral items for this loan is less than the principal loanamount',
            ],
        ]);
    }
}
