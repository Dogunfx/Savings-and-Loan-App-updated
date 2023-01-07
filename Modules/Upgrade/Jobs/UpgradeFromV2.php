<?php

namespace Modules\Upgrade\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetType;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchUser;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientFile;
use Modules\Communication\Entities\SmsGateway;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\CustomField\Entities\CustomField;
use Modules\CustomField\Entities\CustomFieldMeta;
use Modules\Expense\Entities\Expense;
use Modules\Expense\Entities\ExpenseType;
use Modules\Income\Entities\Income;
use Modules\Income\Entities\IncomeType;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanCollateral;
use Modules\Loan\Entities\LoanCollateralType;
use Modules\Loan\Entities\LoanDisbursementChannel;
use Modules\Loan\Entities\LoanFile;
use Modules\Loan\Entities\LoanGuarantor;
use Modules\Loan\Entities\LoanLinkedCharge;
use Modules\Loan\Entities\LoanNote;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanProductLinkedCharge;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsCharge;
use Modules\Savings\Entities\SavingsLinkedCharge;
use Modules\Savings\Entities\SavingsProduct;
use Modules\Savings\Entities\SavingsProductLinkedCharge;
use Modules\Savings\Entities\SavingsTransaction;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;

class UpgradeFromV2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //custom fields
        $custom_fields = DB::connection('old_mysql')
            ->table('custom_fields')
            ->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomField();
            $custom_field->id = $key->id;
            $custom_field->created_by_id = $key->user_id;
            $custom_field->category = $key->category;
            $custom_field->type = $key->field_type;
            $custom_field->name = $key->name;
            $custom_field->required = $key->required;
            $custom_field->save();
            //meta
            foreach (DB::connection('old_mysql')
                         ->table('custom_fields_meta')
                         ->where('custom_field_id', $key->id)
                         ->get() as $item) {
                $custom_field_meta = new CustomFieldMeta();
                $custom_field_meta->id = $item->id;
                $custom_field_meta->parent_id = $item->parent_id;
                $custom_field_meta->custom_field_id = $item->custom_field_id;
                $custom_field_meta->value = $item->name;
                $custom_field_meta->save();
            }
        }
        //import roles
        $roles = DB::connection('old_mysql')
            ->table('roles')
            ->whereNotIn('id', [1, 2])
            ->get();
        foreach ($roles as $key) {
            $role = Role::create(['name' => $key->name]);
        }
        $users = DB::connection('old_mysql')
            ->table('users')
            ->leftJoin('role_users', 'role_users.user_id', 'users.id')
            ->get();
        foreach ($users as $key) {
            //skip user with id 1
            if ($key->id != 1) {
                $user = User::create([
                    'id' => $key->id,
                    //'name' => 'Admin',
                    'first_name' => $key->first_name,
                    'last_name' => $key->last_name,
                    'address' => $key->address,
                    'phone' => $key->phone,
                    'email' => $key->email,
                    'gender' => $key->gender,
                    'notes' => $key->notes,
                    'city' => $key->city,
                    'last_login' => $key->last_login,
                    //'username' => 'admin',
                    'password' => $key->password,
                    'api_token' => Str::random(60),
                    'google2fa_secret' => null,
                    'email_verified_at' => date("Y-m-d H:i:s")
                ]);
                $role = Role::findById($key->role_id);
                $user->assignRole($role);
            }
        }
        //branches
        $branches = DB::connection('old_mysql')
            ->table('branches')
            ->get();
        foreach ($branches as $key) {
            if ($key->id == 1) {
                $branch = Branch::find($key->id);
            } else {
                $branch = new Branch();
                $branch->id = $key->id;
            }
            $branch->name = $key->name;
            $branch->notes = $key->notes;
            $branch->save();
            foreach (DB::connection('old_mysql')
                         ->table('branch_users')
                         ->get() as $item) {
                $branch_user = new BranchUser();
                $branch_user->id = $key->id;
                $branch_user->created_by_id = $key->created_by_id;
                $branch_user->user_id = $key->user_id;
                $branch_user->branch_id = $key->branch_id;
                $branch_user->created_at = $key->created_at;
                $branch_user->save();
            }
        }
        //chart of accounts
        $chart_of_accounts = DB::connection('old_mysql')
            ->table('chart_of_accounts')
            ->get();
        foreach ($chart_of_accounts as $key) {
            $chart_of_account = new ChartOfAccount();
            $chart_of_account->id = $key->id;
            $chart_of_account->parent_id = $key->parent_id;
            $chart_of_account->name = $key->name;
            $chart_of_account->gl_code = $key->gl_code;
            $chart_of_account->account_type = $key->account_type;
            $chart_of_account->active = $key->active;
            $chart_of_account->notes = $key->notes;
            $chart_of_account->save();
        }
        //clients
        $borrowers = DB::connection('old_mysql')
            ->table('borrowers')
            ->get();
        foreach ($borrowers as $key) {
            $client = new Client();
            $client->id = $key->id;
            $client->branch_id = $key->branch_id;
            $client->created_by_id = $key->user_id;
            $client->country_id = $key->country_id;
            $client->loan_officer_id = $key->loan_officer_id;
            $client->first_name = $key->first_name;
            $client->last_name = $key->last_name;
            $client->gender = strtolower($key->gender);
            $client->mobile = $key->mobile;
            $client->email = $key->email;
            $client->reference = $key->unique_number;
            $client->dob = $key->dob;
            if ($key->active == 1) {
                $client->status = 'active';
            }
            $client->address = $key->address;
            $client->city = $key->city;
            $client->state = $key->state;
            $client->zip = $key->zip;
            $client->photo = $key->photo;
            $client->notes = $key->notes;
            $client->save();
            //check for files
            if (!empty($key->files)) {
                $files = unserialize($key->files);
                foreach ($files as $item) {
                    $client_file = new ClientFile();
                    $client_file->created_by_id = $key->user_id;
                    $client_file->client_id = $key->id;
                    $client_file->name = $item;
                    if (file_exists(public_path('uploads/' . $item))) {
                        Storage::put('public/uploads/clients/' . $item, Storage::disk('main_public')->get('uploads/' . $item));
                    }
                    $client_file->link = $item;
                    $client_file->save();
                }
            }

        }
        //sms gateways
        $sms_gateways = DB::connection('old_mysql')
            ->table('sms_gateways')
            ->get();
        foreach ($sms_gateways as $key) {
            $sms_gateway = new SmsGateway();
            $sms_gateway->id = $key->id;
            $sms_gateway->created_by_id = $key->user_id;
            $sms_gateway->name = $key->name;
            $sms_gateway->to_name = $key->to_name;
            $sms_gateway->url = $key->url;
            $sms_gateway->msg_name = $key->msg_name;
            $sms_gateway->active = 1;
            $sms_gateway->notes = $key->notes;
            $sms_gateway->save();
        }
        //loan collateral types
        $collateral_types = DB::connection('old_mysql')
            ->table('collateral_types')
            ->get();
        foreach ($collateral_types as $key) {
            $loan_collateral_type = new LoanCollateralType();
            $loan_collateral_type->id = $key->id;
            $loan_collateral_type->name = $key->name;
            $loan_collateral_type->save();
        }
        //loan disbursement channels
        $loan_disbursed_by = DB::connection('old_mysql')
            ->table('loan_disbursed_by')
            ->get();
        foreach ($loan_disbursed_by as $key) {
            $loan_disbursement_channel = new LoanDisbursementChannel();
            $loan_disbursement_channel->id = $key->id;
            $loan_disbursement_channel->name = $key->name;
            $loan_disbursement_channel->save();
        }
        //payment types
        $payment_types = DB::connection('old_mysql')
            ->table('payment_types')
            ->get();
        foreach ($payment_types as $key) {
            $payment_type = new PaymentType();
            $payment_type->id = $key->id;
            $payment_type->name = $key->name;
            $payment_type->save();
        }
        //loan charges
        $charges = DB::connection('old_mysql')
            ->table('charges')
            ->where('product', 'loan')
            ->get();
        foreach ($charges as $key) {
            $loan_charge = new LoanCharge();
            $loan_charge->id = $key->id;
            $loan_charge->name = $key->name;
            $loan_charge->created_by_id = $key->user_id;
            $loan_charge->currency_id = $key->currency_id;
            $loan_charge->amount = $key->amount;
            $loan_charge->is_penalty = $key->penalty;
            $loan_charge->active = $key->active;
            $loan_charge->schedule = $key->charge_frequency;
            $loan_charge->schedule_frequency = $key->charge_frequency_amount;
            $loan_charge->schedule_frequency_type = $key->charge_frequency_type;
            if ($key->charge_type == 'disbursement') {
                $loan_charge->loan_charge_type_id = 1;
            }
            if ($key->charge_type == 'specified_due_date') {
                $loan_charge->loan_charge_type_id = 2;
            }
            if ($key->charge_type == 'installment_fee') {
                $loan_charge->loan_charge_type_id = 3;
            }
            if ($key->charge_type == 'overdue_installment_fee') {
                $loan_charge->loan_charge_type_id = 4;
            }
            if ($key->charge_type == 'loan_rescheduling_fee') {
                $loan_charge->loan_charge_type_id = 6;
            }
            if ($key->charge_type == 'overdue_maturity') {
                $loan_charge->loan_charge_type_id = 7;
            }
            if ($key->charge_type == 'disbursement') {
                $loan_charge->loan_charge_option_id = 1;
            }
            if ($key->charge_option == 'fixed') {
                $loan_charge->loan_charge_option_id = 1;
            }
            if ($key->charge_option == 'principal_due') {
                $loan_charge->loan_charge_option_id = 2;
            }
            if ($key->charge_option == 'principal_interest') {
                $loan_charge->loan_charge_option_id = 3;
            }
            if ($key->charge_option == 'interest_due') {
                $loan_charge->loan_charge_option_id = 4;
            }
            if ($key->charge_option == 'total_due') {
                $loan_charge->loan_charge_option_id = 5;
            }
            if ($key->charge_option == 'original_principle') {
                $loan_charge->loan_charge_option_id = 7;
            }
            $loan_charge->save();
        }
        //loan products
        $loan_products = DB::connection('old_mysql')
            ->table('loan_products')
            ->get();
        foreach ($loan_products as $key) {
            $loan_product = new LoanProduct();
            $loan_product->id = $key->id;
            $loan_product->created_by_id = $key->user_id;
            $loan_product->currency_id = $key->currency_id;
            if ($key->loan_transaction_strategy == 'penalty_fees_interest_principal') {
                $loan_product->loan_transaction_processing_strategy_id = 1;
            }
            if ($key->loan_transaction_strategy == 'principal_interest_penalty_fees') {
                $loan_product->loan_transaction_processing_strategy_id = 2;
            }
            if ($key->loan_transaction_strategy == 'interest_principal_penalty_fees') {
                $loan_product->loan_transaction_processing_strategy_id = 3;
            }
            $loan_product->loan_disbursement_channel_id = $key->loan_disbursed_by_id;
            $loan_product->name = $key->name;
            $loan_product->short_name = $key->name;
            if ($key->decimal_places == 'round_off_to_two_decimal') {
                $loan_product->decimals = 2;
            } else {
                $loan_product->decimals = 0;
            }
            $loan_product->minimum_principal = $key->minimum_principal;
            $loan_product->default_principal = $key->default_principal;
            $loan_product->maximum_principal = $key->maximum_principal;
            $loan_product->minimum_loan_term = $key->default_loan_duration;
            $loan_product->default_loan_term = $key->default_loan_duration;
            $loan_product->maximum_loan_term = $key->default_loan_duration;
            if ($key->repayment_cycle == 'daily') {
                $loan_product->repayment_frequency = 1;
                $loan_product->repayment_frequency_type = 'days';
            }
            if ($key->repayment_cycle == 'weekly') {
                $loan_product->repayment_frequency = 1;
                $loan_product->repayment_frequency_type = 'weeks';
            }
            if ($key->repayment_cycle == 'monthly') {
                $loan_product->repayment_frequency = 1;
                $loan_product->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'bi_monthly') {
                $loan_product->repayment_frequency = 2;
                $loan_product->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'quartely') {
                $loan_product->repayment_frequency = 4;
                $loan_product->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'semi_annually') {
                $loan_product->repayment_frequency = 6;
                $loan_product->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'annually') {
                $loan_product->repayment_frequency = 12;
                $loan_product->repayment_frequency_type = 'months';
            }
            $loan_product->minimum_interest_rate = $key->minimum_interest_rate;
            $loan_product->default_interest_rate = $key->default_interest_rate;
            $loan_product->maximum_interest_rate = $key->maximum_interest_rate;
            $loan_product->interest_rate_type = $key->interest_period;
            $loan_product->grace_on_interest_charged = $key->grace_on_interest_charged;
            if ($key->interest_method == 'flat_rate') {
                $loan_product->interest_methodology = 'flat';
            }
            if ($key->interest_method == 'declining_balance_equal_installments') {
                $loan_product->interest_methodology = 'declining_balance';
                $loan_product->amortization_method = 'equal_installments';
            }
            if ($key->interest_method == 'declining_balance_equal_principal') {
                $loan_product->interest_methodology = 'declining_balance';
                $loan_product->amortization_method = 'equal_principal_payments';
            }
            if ($key->accounting_rule == 'cash_based') {
                $loan_product->accounting_rule = 'cash';
            } else {
                $loan_product->accounting_rule = $key->accounting_rule;
            }
            $loan_product->active = 1;
            $loan_product->fund_source_chart_of_account_id = $key->chart_fund_source_id;
            $loan_product->loan_portfolio_chart_of_account_id = $key->chart_loan_portfolio_id;
            $loan_product->interest_receivable_chart_of_account_id = $key->chart_receivable_interest_id;
            $loan_product->penalties_receivable_chart_of_account_id = $key->chart_receivable_penalty_id;
            $loan_product->fees_receivable_chart_of_account_id = $key->chart_receivable_fee_id;
            $loan_product->fees_chart_of_account_id = $key->chart_receivable_fee_id;
            $loan_product->overpayments_chart_of_account_id = $key->chart_loan_over_payments_id;
            $loan_product->income_from_interest_chart_of_account_id = $key->income_from_interest_chart_of_account_id;
            $loan_product->income_from_penalties_chart_of_account_id = $key->chart_income_interest_id;
            $loan_product->income_from_fees_chart_of_account_id = $key->chart_income_fee_id;
            $loan_product->income_from_recovery_chart_of_account_id = $key->chart_income_recovery_id;
            $loan_product->losses_written_off_chart_of_account_id = $key->chart_loans_written_off_id;
            $loan_product->save();


        }
        //charges
        $loan_product_charges = DB::connection('old_mysql')
            ->table('loan_product_charges')
            ->get();
        foreach ($loan_product_charges as $key) {
            $loan_product_charge = new LoanProductLinkedCharge();
            $loan_product_charge->id = $key->id;
            $loan_product_charge->loan_product_id = $key->loan_product_id;
            $loan_product_charge->loan_charge_id = $key->charge_id;
            $loan_product_charge->save();
        }
        //loan collateral
        $collateral = DB::connection('old_mysql')
            ->table('collateral')
            ->get();
        foreach ($collateral as $key) {
            $loan_collateral = new LoanCollateral();
            $loan_collateral->loan_id = $key->loan_id;
            $loan_collateral->loan_collateral_type_id = $key->collateral_type_id;
            $loan_collateral->value = $key->value;
            $loan_collateral->link = $key->photo;
            $loan_collateral->description = $key->notes;
            if (file_exists(public_path('uploads/' . $key->photo))) {
                Storage::put('public/uploads/loans/' . $key->photo, Storage::disk('main_public')->get('uploads/' . $key->photo));
            }
            $loan_collateral->save();
        }
        //loan comments
        $loan_comments = DB::connection('old_mysql')
            ->table('loan_comments')
            ->get();
        foreach ($loan_comments as $key) {
            $loan_note = new LoanNote();
            $loan_note->created_by_id = $key->user_id;
            $loan_note->loan_id = $key->loan_id;
            $loan_note->description = $key->notes;
            $loan_note->created_at = $key->created_at;
            $loan_note->save();
        }
        //loan guarantors
        $guarantor = DB::connection('old_mysql')
            ->table('guarantor')
            ->get();
        foreach ($guarantor as $key) {
            $loan_guarantor = new LoanGuarantor();
            $loan_guarantor->created_by_id = $key->user_id;
            $loan_guarantor->loan_id = $key->loan_id;
            $loan_guarantor->client_id = $key->borrower_id;
            $loan_guarantor->is_client = 0;
            $loan_guarantor->first_name = $key->first_name;
            $loan_guarantor->last_name = $key->last_name;
            $loan_guarantor->gender = strtolower($key->gender);
            $loan_guarantor->country_id = $key->country_id;
            $loan_guarantor->mobile = $key->mobile;
            $loan_guarantor->notes = $key->notes;
            $loan_guarantor->email = $key->email;
            $loan_guarantor->address = $key->address;
            $loan_guarantor->marital_status = $key->marital_status;
            $loan_guarantor->dob = $key->dob;
            $loan_guarantor->guaranteed_amount = $key->accepted_amount;
            if (file_exists(public_path('uploads/' . $key->photo))) {
                Storage::put('public/uploads/loans/' . $key->photo, Storage::disk('main_public')->get('uploads/' . $key->photo));
            }
            $loan_guarantor->save();
        }
        $loan_guarantors = DB::connection('old_mysql')
            ->table('loan_guarantors')
            ->get();
        foreach ($loan_guarantors as $key) {
            LoanGuarantor::update(['loan_id', $key->loan_id], ['id', $key->guarantor_id]);
        }
        //
        $loan_charges = DB::connection('old_mysql')
            ->table('loan_charges')
            ->get();
        foreach ($loan_charges as $key) {
            $loan_charge = LoanCharge::find($key->id);
            $loan_linked_charge = new LoanLinkedCharge();
            $loan_linked_charge->loan_id = $key->loan_id;
            $loan_linked_charge->name = $loan_charge->name;
            $loan_linked_charge->loan_charge_id = $key->id;
            if ($loan_charge->allow_override == 1) {
                $loan_linked_charge->amount = $key->amount;
            } else {
                $loan_linked_charge->amount = $loan_charge->amount;
            }
            $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
            $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
            $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
            $loan_linked_charge->save();
        }
        //loans
        $loans = DB::connection('old_mysql')
            ->table('loans')
            ->get();
        foreach ($loans as $key) {
            $loan_product = LoanProduct::find($key->loan_product_id);
            $loan = new Loan();
            $loan->currency_id = $loan_product->currency_id;
            $loan->loan_product_id = $key->loan_product_id;
            $loan->client_id = $key->borrower_id;
            $loan->branch_id = $key->branch_id;
            $loan->loan_transaction_processing_strategy_id = $loan_product->loan_transaction_processing_strategy_id;
            //$loan->loan_purpose_id = $key->loan_purpose_id;
            $loan->loan_officer_id = $key->loan_officer_id;
            $loan->expected_disbursement_date = $key->release_date;
            $loan->expected_first_payment_date = $key->first_payment_date;
            $loan->first_payment_date = $key->first_payment_date;
            $loan->expected_maturity_date = $key->maturity_date;
            //$loan->fund_id = $key->fund_id;
            $loan->created_by_id = $key->user_id;
            $loan->applied_amount = $key->applied_amount;
            $loan->approved_amount = $key->approved_amount;
            $loan->loan_term = $key->loan_duration;
            if ($key->repayment_cycle == 'daily') {
                $loan->repayment_frequency = 1;
                $loan->repayment_frequency_type = 'days';
            }
            if ($key->repayment_cycle == 'weekly') {
                $loan->repayment_frequency = 1;
                $loan->repayment_frequency_type = 'weeks';
            }
            if ($key->repayment_cycle == 'monthly') {
                $loan->repayment_frequency = 1;
                $loan->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'bi_monthly') {
                $loan->repayment_frequency = 2;
                $loan->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'quartely') {
                $loan->repayment_frequency = 4;
                $loan->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'semi_annually') {
                $loan->repayment_frequency = 6;
                $loan->repayment_frequency_type = 'months';
            }
            if ($key->repayment_cycle == 'annually') {
                $loan->repayment_frequency = 12;
                $loan->repayment_frequency_type = 'months';
            }
            $loan->interest_rate = $key->interest_rate;
            $loan->interest_rate_type = $loan_product->interest_period;
            $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
            $loan->interest_methodology = $loan_product->interest_methodology;
            $loan->amortization_method = $loan_product->amortization_method;
            // $loan->auto_disburse = $loan_product->auto_disburse;
            $loan->submitted_on_date = Carbon::parse($key->created_at)->format('Y-d-m');
            $loan->submitted_by_user_id = $key->user_id;
            $generate_schedule = false;
            !empty($key->disbursed_date) ? $loan->disbursed_on_date = $key->disbursed_date : '';
            !empty($key->disbursed_notes) ? $loan->disbursed_notes = $key->disbursed_notes : '';
            !empty($key->disbursed_by_id) ? $loan->disbursed_by_user_id = $key->disbursed_by_id : '';
            !empty($key->approved_date) ? $loan->approved_on_date = $key->approved_date : '';
            !empty($key->approved_notes) ? $loan->approved_notes = $key->approved_notes : '';
            !empty($key->approved_by_id) ? $loan->approved_by_user_id = $key->approved_by_id : '';
            !empty($key->written_off_date) ? $loan->written_off_on_date = $key->written_off_date : '';
            !empty($key->written_off_notes) ? $loan->written_off_notes = $key->written_off_notes : '';
            !empty($key->written_off_by_id) ? $loan->written_off_by_user_id = $key->written_off_by_id : '';
            !empty($key->declined_date) ? $loan->rejected_on_date = $key->declined_date : '';
            !empty($key->declined_notes) ? $loan->rejected_notes = $key->declined_notes : '';
            !empty($key->declined_by_id) ? $loan->rejected_by_user_id = $key->declined_by_id : '';
            !empty($key->closed_date) ? $loan->closed_on_date = $key->closed_date : '';
            !empty($key->closed_notes) ? $loan->closed_notes = $key->closed_notes : '';
            !empty($key->closed_by_id) ? $loan->closed_by_user_id = $key->closed_by_id : '';
            !empty($key->rescheduled_date) ? $loan->rescheduled_on_date = $key->rescheduled_date : '';
            !empty($key->rescheduled_notes) ? $loan->rescheduled_notes = $key->rescheduled_notes : '';
            !empty($key->rescheduled_by_id) ? $loan->rescheduled_by_user_id = $key->rescheduled_by_id : '';
            !empty($key->withdrawn_date) ? $loan->withdrawn_on_date = $key->withdrawn_date : '';
            !empty($key->withdrawn_notes) ? $loan->withdrawn_notes = $key->withdrawn_notes : '';
            !empty($key->withdrawn_by_id) ? $loan->withdrawn_by_user_id = $key->withdrawn_by_id : '';
            if ($key->status == 'pending') {
                $loan->status = 'submitted';
            }
            if ($key->status == 'approved') {
                $loan->status = 'approved';
            }
            if ($key->status == 'disbursed') {
                $generate_schedule = true;
                $loan->status = 'active';
            }
            if ($key->status == 'declined') {
                $loan->status = 'rejected';
            }
            if ($key->status == 'withdrawn') {
                $loan->status = 'withdrawn';
            }
            if ($key->status == 'written_off') {
                $generate_schedule = true;
                $loan->status = 'written_off';
            }
            if ($key->status == 'closed') {
                $generate_schedule = true;
                $loan->status = 'closed';
            }
            if ($key->status == 'rescheduled') {
                $generate_schedule = true;
                $loan->status = 'rescheduled';
            }
            $loan->save();
            //check for files
            if (!empty($key->files)) {
                $files = unserialize($key->files);
                foreach ($files as $item) {
                    $loan_file = new LoanFile();
                    $loan_file->created_by_id = $key->user_id;
                    $loan_file->loan_id = $key->id;
                    $loan_file->name = $item;
                    if (file_exists(public_path('uploads/' . $item))) {
                        Storage::put('public/uploads/loans/' . $item, Storage::disk('main_public')->get('uploads/' . $item));
                    }
                    $loan_file->link = $item;
                    $loan_file->save();
                }
            }
        }
        //loan transactions
        $loan_transactions = DB::connection('old_mysql')
            ->table('loan_transactions')
            ->get();
        foreach ($loan_transactions as $key) {
            $payment_detail = new PaymentDetail();
            $payment_detail->created_by_id = $key->user_id;
            $payment_detail->payment_type_id = $key->repayment_method_id;
            $payment_detail->transaction_type = 'loan_transaction';
            $payment_detail->save();
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = $key->user_id;
            $loan_transaction->branch_id = $key->branch_id;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->loan_id = $key->loan_id;
            $loan_transaction->name = $key->name;
            if ($key->transaction_type == 'repayment') {
                $loan_transaction->loan_transaction_type_id = 2;
            }
            if ($key->transaction_type == 'disbursement') {
                $loan_transaction->loan_transaction_type_id = 1;
            }
            if ($key->transaction_type == 'repayment_disbursement') {
                $loan_transaction->loan_transaction_type_id = 5;
            }
            if ($key->transaction_type == 'write_off') {
                $loan_transaction->loan_transaction_type_id = 6;
            }
            if ($key->transaction_type == 'write_off_recovery') {
                $loan_transaction->loan_transaction_type_id = 8;
            }
            if ($key->transaction_type == 'interest') {
                $loan_transaction->loan_transaction_type_id = 11;
            }
            if ($key->transaction_type == 'disbursement_fee') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'installment_fee') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'specified_due_date_fee') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'overdue_maturity') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'overdue_installment_fee') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'loan_rescheduling_fee') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'penalty') {
                $loan_transaction->loan_transaction_type_id = 10;
            }
            if ($key->transaction_type == 'interest_waiver') {
                $loan_transaction->loan_transaction_type_id = 4;
            }
            if ($key->transaction_type == 'charge_waiver') {
                $loan_transaction->loan_transaction_type_id = 9;
            }

            $loan_transaction->submitted_on = $key->date;
            $loan_transaction->created_on = Carbon::parse($key->created_at)->format('Y-m-d');
            $loan_transaction->amount = $key->debit ?: $key->credit;
            $loan_transaction->debit = $key->debit;
            $loan_transaction->credit = $key->credit;
            $loan_transaction->reversible = $key->reversible;
            $loan_transaction->reversed = $key->reversed;
            $loan_transaction->save();
        }
        //assets
        $asset_types = DB::connection('old_mysql')
            ->table('asset_types')
            ->get();
        foreach ($asset_types as $key) {
            $asset_type = new AssetType();
            $asset_type->id = $key->id;
            $asset_type->name = $key->name;
            $asset_type->type = $key->type;
            $asset_type->save();

        }
        $assets = DB::connection('old_mysql')
            ->table('assets')
            ->get();
        foreach ($assets as $key) {
            $asset = new Asset();
            $asset->created_by_id = $key->user_id;
            $asset->asset_type_id = $key->asset_type_id;
            $asset->branch_id = $key->branch_id;
            $asset->purchase_date = $key->purchase_date;
            $asset->purchase_price = $key->purchase_price;
            $asset->value = $key->purchase_price;
            $asset->life_span = $key->life_span;
            $asset->salvage_value = $key->salvage_value;
            $asset->notes = $key->notes;
            $asset->created_at = $key->created_at;
            $asset->save();

        }
        //expenses
        $expense_types = DB::connection('old_mysql')
            ->table('expense_types')
            ->get();
        foreach ($expense_types as $key) {
            $expense_type = new ExpenseType();
            $expense_type->id = $key->id;
            $expense_type->name = $key->name;
            $expense_type->expense_chart_of_account_id = $key->account_id;
            $expense_type->save();
        }
        $expenses = DB::connection('old_mysql')
            ->table('expenses')
            ->get();
        foreach ($expenses as $key) {
            $expense = new Expense();
            $expense->id = $key->id;
            $expense->created_by_id = $key->user_id;
            $expense->expense_type_id = $key->expense_type_id;
            $expense->currency_id = $key->currency_id;
            $expense->branch_id = $key->branch_id;
            $expense->expense_chart_of_account_id = $key->account_id;
            $expense->asset_chart_of_account_id = $key->chart_id;
            $expense->amount = $key->amount;
            $expense->date = $key->date;
            $expense->recurring = $key->recurring;
            $expense->recur_frequency = $key->recur_frequency;
            $expense->recur_start_date = $key->recur_start_date;
            $expense->recur_end_date = $key->recur_end_date;
            $expense->recur_next_date = $key->recur_next_date;
            $expense->recur_type = $key->recur_type;
            $expense->description = $key->notes;
            $expense->created_at = $key->created_at;
            $expense->save();
        }
        //income
        $income_types = DB::connection('old_mysql')
            ->table('income_types')
            ->get();
        foreach ($income_types as $key) {
            $income_type = new IncomeType();
            $income_type->id = $key->id;
            $income_type->name = $key->name;
            $income_type->income_chart_of_account_id = $key->income_chart_of_account_id;
            $income_type->save();
        }
        $incomes = DB::connection('old_mysql')
            ->table('income')
            ->get();
        foreach ($incomes as $key) {
            $income = new Income();
            $income->id = $key->id;
            $income->created_by_id = $key->user_id;
            $income->income_type_id = $key->income_type_id;
            $income->currency_id = $key->currency_id;
            $income->branch_id = $key->branch_id;
            $income->income_chart_of_account_id = $key->account_id;
            $income->asset_chart_of_account_id = $key->chart_id;
            $income->amount = $key->amount;
            $income->date = $key->date;
            $income->description = $key->notes;
            $income->created_at = $key->created_at;
            $income->save();
        }
        //journal entries
        $journal_entries = DB::connection('old_mysql')
            ->table('journal_entries')
            ->get();
        foreach ($journal_entries as $key) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = $key->id;
            $journal_entry->transaction_number = $key->reference;
            $journal_entry->branch_id = $key->branch_id;
            $journal_entry->currency_id = $key->currency_id;
            $journal_entry->chart_of_account_id = $key->account_id;
            $journal_entry->transaction_type = $key->transaction_type;
            $journal_entry->date = $key->date;
            $journal_entry->month = $key->month;
            $journal_entry->year = $key->year;
            $journal_entry->debit = $key->debit;
            $journal_entry->credit = $key->credit;
            $journal_entry->balance = $key->balance;
            $journal_entry->reference = $key->reference;
            $journal_entry->reversed = $key->reversed;
            $journal_entry->notes = $key->notes;
            $journal_entry->created_at = $key->created_at;
            $journal_entry->save();
        }
        //savings charges
        $savings_charges = DB::connection('old_mysql')
            ->table('charges')
            ->where('product', 'savings')
            ->get();
        foreach ($savings_charges as $key) {
            $savings_charge = new SavingsCharge();
            $savings_charge->id = $key->id;
            $savings_charge->name = $key->name;
            $savings_charge->created_by_id = $key->user_id;
            $savings_charge->currency_id = $key->currency_id;
            $savings_charge->amount = $key->amount;
            $savings_charge->active = $key->active;
            if ($key->charge_type == 'savings_activation') {
                $savings_charge->savings_charge_type_id = 1;
            }
            if ($key->charge_type == 'specified_due_date') {
                $savings_charge->savings_charge_type_id = 2;
            }
            if ($key->charge_type == 'withdrawal_fee') {
                $savings_charge->savings_charge_type_id = 3;
            }
            if ($key->charge_type == 'annual_fee') {
                $savings_charge->savings_charge_type_id = 4;
            }
            if ($key->charge_type == 'monthly_fee') {
                $savings_charge->savings_charge_type_id = 5;
            }
            if ($key->charge_option == 'fixed') {
                $savings_charge->savings_charge_option_id = 1;
            }
            if ($key->charge_option == 'percentage') {
                $savings_charge->savings_charge_option_id = 2;
            }
            $savings_charge->save();
        }
        //savings products
        $savings_products = DB::connection('old_mysql')
            ->table('savings_products')
            ->get();
        foreach ($savings_products as $key) {
            $savings_product = new SavingsProduct();
            $savings_product->id = $key->id;
            $savings_product->created_by_id = $key->user_id;
            $savings_product->currency_id = $key->currency_id;
            $savings_product->savings_reference_chart_of_account_id = $key->chart_reference_id;
            $savings_product->overdraft_portfolio_chart_of_account_id = $key->chart_overdraft_portfolio_id;
            $savings_product->savings_control_chart_of_account_id = $key->chart_savings_control_id;
            $savings_product->interest_on_savings_chart_of_account_id = $key->chart_expense_interest_id;
            $savings_product->write_off_savings_chart_of_account_id = $key->chart_expense_written_off_id;
            $savings_product->income_from_fees_chart_of_account_id = $key->chart_income_fee_id;
            $savings_product->income_from_penalties_chart_of_account_id = $key->chart_income_penalty_id;
            $savings_product->income_from_interest_overdraft_chart_of_account_id = $key->chart_income_interest_id;
            $savings_product->savings_category = 'voluntary';
            $savings_product->default_interest_rate = $key->interest_rate;
            $savings_product->compounding_period = $key->interest_compounding_period;
            $savings_product->interest_posting_period_type = $key->interest_posting_period;
            $savings_product->name = $key->name;
            $savings_product->short_name = $key->name;
            $savings_product->description = $key->notes;
            $savings_product->decimals = 2;
            $savings_product->interest_calculation_type = ($key->interest_calculation_type == 'daily') ? 'daily_balance' : 'average_balance';
            $savings_product->minimum_balance_for_interest_calculation = $key->minimum_balance;
            $savings_product->lockin_period = $key->lockin_period;
            $savings_product->allow_overdraft = $key->allow_overdraw;
            $savings_product->accounting_rule = ($key->accounting_rule == 'cash_based') ? 'cash' : 'none';
            $savings_product->active = $key->active;
            $savings_products->save();


        }
        //charges
        $savings_product_charges = DB::connection('old_mysql')
            ->table('savings_product_charges')
            ->get();
        foreach ($savings_product_charges as $key) {
            $savings_product_charge = new SavingsProductLinkedCharge();
            $savings_product_charge->id = $key->id;
            $savings_product_charge->savings_product_id = $key->savings_product_id;
            $savings_product_charge->savings_charge_id = $key->charge_id;
            $savings_product_charge->amount = $key->amount;
            $savings_product_charge->save();
        }
        //savings
        $savings = DB::connection('old_mysql')
            ->table('savings')
            ->get();
        foreach ($savings as $key) {
            $savings = new Savings();
            $savings->created_by_id = $key->user_id;
            $savings->client_id = $key->borrower_id;
            $savings->savings_product_id = $key->savings_product_id;
            $savings->savings_officer_id = $key->loan_officer_id;
            $savings->branch_id = $key->branch_id;
            $savings->start_interest_posting_date = $key->start_interest_posting_date;
            $savings->next_interest_posting_date = $key->next_interest_posting_date;
            $savings->start_interest_calculation_date = $key->start_interest_calculation_date;
            $savings->next_interest_calculation_date = $key->next_interest_calculation_date;
            $savings->last_interest_calculation_date = $key->last_interest_calculation_date;
            $savings->last_interest_posting_date = $key->last_interest_posting_date;
            $savings->calculated_interest = $key->interest_earned;
            $savings->overdraft_limit = $key->overdraft_limit;
            $savings->overdraft_interest_rate = $key->overdraft_interest_rate;
            $savings->submitted_on_date = $key->date;
            $savings->submitted_by_user_id = $key->user_id;
            $savings->created_at = $key->created_at;
            $savings->approved_on_date = $key->approved_date;
            $savings->rejected_on_date = $key->declined_date;
            $savings->closed_on_date = $key->closed_date;
            $savings->approved_notes = $key->approved_notes;
            $savings->rejected_notes = $key->declined_notes;
            $savings->closed_notes = $key->closed_notes;
            $savings->save();
        }
        //savings linked charges
        $savings_charges = DB::connection('old_mysql')
            ->table('savings_charges')
            ->get();
        foreach ($savings_charges as $key) {
            $savings_linked_charge = new SavingsLinkedCharge();
            $savings_linked_charge->id = $key->id;
            $savings_linked_charge->savings_id = $key->savings_id;
            $savings_linked_charge->savings_charge_id = $key->charge_id;
            $savings_linked_charge->name = $key->name;
            $savings_linked_charge->amount = $key->amount;
            $savings_linked_charge->save();
        }
        //savings transactions
        $savings_transactions = DB::connection('old_mysql')
            ->table('savings_transactions')
            ->get();
        foreach ($savings_transactions as $key) {
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->id = $key->id;
            $savings_transaction->created_by_id = $key->user_id;
            $savings_transaction->savings_id = $key->savings_id;
            $savings_transaction->branch_id = $key->branch_id;
            if ($key->type == 'deposit') {
                $savings_transaction->name = trans_choice('savings::general.deposit', 1);
                $savings_transaction->savings_transaction_type_id = 1;
            }
            if ($key->type == 'withdrawal') {
                $savings_transaction->name = trans_choice('savings::general.withdrawal', 1);
                $savings_transaction->savings_transaction_type_id = 2;
            }
            if ($key->type == 'interest') {
                $savings_transaction->name = trans_choice('savings::general.apply', 1) . ' ' . trans_choice('savings::general.interest', 1);
                $savings_transaction->savings_transaction_type_id = 11;
            }
            if ($key->type == 'charge') {
                $savings_transaction->name = trans_choice('savings::general.apply', 1) . ' ' . trans_choice('savings::general.charge', 1);
                $savings_transaction->savings_transaction_type_id = 10;
            }
            $savings_transaction->reversible = $key->reversible;
            $savings_transaction->submitted_on = $key->date;
            $savings_transaction->created_on = $key->date;
            $savings_transaction->amount = $key->debit ?: $key->credit;
            $savings_transaction->debit = $key->debit;
            $savings_transaction->credit = $key->credit;
            $savings_transaction->balance = $key->balance;
            $savings_transaction->reversed = $key->reversed;
            $savings_transaction->status = 'approved';
            $savings_transaction->created_at = $key->created_at;
            $savings_transaction->save();
        }
    }
}
