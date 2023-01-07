<?php

use Modules\Communication\Entities\SmsGateway;
use Modules\Setting\Entities\Setting;
use Modules\Client\Entities\Client;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Communication\Entities\CommunicationCampaignLog;
use Illuminate\Support\Carbon;
use \Modules\Loan\Entities\LoanRepaymentSchedule;
use \Modules\Savings\Entities\Savings;
use \Modules\Savings\Entities\SavingsTransaction;

/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/23/19
 * Time: 3:22 PM
 */
if (!function_exists('send_sms')) {

    /**
     * Send sms to an HTTP API using curl
     * @param string $to The number to send the message to
     * @param string $msg The message to be sent
     * @param null $gateway_id Provide gateway id
     */
    function send_sms($to, $msg, $gateway_id = null)
    {
        if (!$gateway_id) {
            $active_sms_gateway = SmsGateway::find(Setting::where('setting_key',
                'communication.active_sms_gateway')->first()->setting_value);
        } else {
            $active_sms_gateway = SmsGateway::find($gateway_id);
        }
        if ($active_sms_gateway) {
            $append = "&";
            $append .= $active_sms_gateway->to_name . "=" . $to;
            $append .= "&" . $active_sms_gateway->msg_name . "=" . urlencode($msg);
            $url = $active_sms_gateway->url . $append;
            //send sms here
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        }

    }
}
if (!function_exists('template_replace_tags')) {

    /**
     * Replaces tags in templates
     * @param array $args An array of arguments
     * @return string $body
     */
    function template_replace_tags(array $args)
    {
        $body = $args['body'];
        //client
        if (array_key_exists('client_id', $args)) {
            $client = Client::with('title')->with('profession')->with('branch')->with('country')->with('client_type')->with('loan_officer')->find($args['client_id']);
            $body = str_replace('{{client_first_name}}', $client->first_name, $body);
            $body = str_replace('{{client_middle_name}}', $client->middle_name, $body);
            $body = str_replace('{{client_last_name}}', $client->last_name, $body);
            $body = str_replace('{{client_mobile}}', $client->mobile, $body);
            $body = str_replace('{{client_phone}}', $client->phone, $body);
            $body = str_replace('{{client_external_id}}', $client->external_id, $body);
            $body = str_replace('{{client_email}}', $client->email, $body);
            $body = str_replace('{{client_dob}}', $client->dob, $body);
            $body = str_replace('{{client_id}}', $client->id, $body);
            $body = str_replace('{{client_address}}', $client->address, $body);
            $body = str_replace('{{client_zip}}', $client->zip, $body);
            $body = str_replace('{{client_state}}', $client->state, $body);
            $body = str_replace('{{client_city}}', $client->city, $body);
            if ($client->gender == 'male') {
                $body = str_replace('{{client_gender}}', trans_choice('client::gender.male', 1), $body);
            }
            if ($client->gender == 'female') {
                $body = str_replace('{{client_gender}}', trans_choice('client::gender.female', 1), $body);
            }
            if (!empty($client->country)) {
                $body = str_replace('{{client_country}}', $client->country->name, $body);
            }
            if (!empty($client->profession)) {
                $body = str_replace('{{client_profession}}', $client->profession->name, $body);
            }
            if (!empty($client->title)) {
                $body = str_replace('{{client_title}}', $client->title->name, $body);
            }
            if (!empty($client->branch)) {
                $body = str_replace('{{client_branch}}', $client->branch->name, $body);
            }
            if (!empty($client->loan_officer)) {
                $body = str_replace('{{client_loan_officer}}', $client->loan_officer->first_name . ' ' . $client->loan_officer->last_name, $body);
            }
        }
        //loan
        if (array_key_exists('loan_id', $args)) {
            $loan = Loan::with('repayment_schedules')->with('loan_product')->with('branch')->with('loan_officer')->with('currency')->with('fund')->with('loan_purpose')->find($args['loan_id']);
            $body = str_replace('{{loan_id}}', $loan->id, $body);
            if ($loan->status == 'pending') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1), $body);
            }
            if ($loan->status == 'submitted') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.pending_approval', 1), $body);
            }
            if ($loan->status == 'overpaid') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.overpaid', 1), $body);
            }
            if ($loan->status == 'approved') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.awaiting_disbursement', 1), $body);
            }
            if ($loan->status == 'active') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.active', 1), $body);
            }
            if ($loan->status == 'rejected') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.rejected', 1), $body);
            }
            if ($loan->status == 'withdrawn') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.withdrawn', 1), $body);
            }
            if ($loan->status == 'written_off') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.written_off', 1), $body);
            }
            if ($loan->status == 'closed') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.closed', 1), $body);
            }
            if ($loan->status == 'pending_reschedule') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.pending_reschedule', 1), $body);
            }
            if ($loan->status == 'rescheduled') {
                $body = str_replace('{{loan_status}}', trans_choice('loan::general.rescheduled', 1), $body);
            }
            if (!empty($loan->loan_purpose)) {
                $body = str_replace('{{loan_purpose}}', $loan->loan_purpose->name, $body);
            }
            if (!empty($loan->fund)) {
                $body = str_replace('{{loan_fund}}', $loan->fund->name, $body);
            }
            if (!empty($loan->currency)) {
                $body = str_replace('{{loan_currency_name}}', $loan->currency->name, $body);
                $body = str_replace('{{loan_currency_symbol}}', $loan->currency->symbol, $body);
            }
            if (!empty($loan->branch)) {
                $body = str_replace('{{loan_branch}}', $loan->branch->name, $body);
            }
            if (!empty($loan->loan_officer)) {
                $body = str_replace('{{loan_officer}}', $loan->loan_officer->first_name . ' ' . $loan->loan_officer->last_name, $body);
            }
            //arrears
            $arrears_days = 0;
            $arrears_amount = 0;
            $arrears_last_schedule = $loan->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
            if (!empty($arrears_last_schedule)) {
                $overdue_schedules = $loan->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);
                $arrears_amount = $arrears_amount + $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived') + $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived') + $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived') + $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');
                $arrears_days = $arrears_days + Carbon::today()->diffInDays(Carbon::parse($overdue_schedules->sortBy('due_date')->due_date));
            }
            $principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_written_off_derived') - $loan->repayment_schedules->sum('principal_repaid_derived');
            $interest = $loan->repayment_schedules->sum('interest') - $loan->repayment_schedules->sum('interest_written_off_derived') - $loan->repayment_schedules->sum('interest_repaid_derived') - $loan->repayment_schedules->sum('interest_waived_derived');
            $fees = $loan->repayment_schedules->sum('fees') - $loan->repayment_schedules->sum('fees_written_off_derived') - $loan->repayment_schedules->sum('fees_repaid_derived') - $loan->repayment_schedules->sum('fees_waived_derived');
            $penalties = $loan->repayment_schedules->sum('penalties') - $loan->repayment_schedules->sum('penalties_written_off_derived') - $loan->repayment_schedules->sum('penalties_repaid_derived') - $loan->repayment_schedules->sum('penalties_waived_derived');
            $balance = $principal + $interest + $fees + $penalties;
            $total_due = $loan->repayment_schedules->sum('principal') + $loan->repayment_schedules->sum('interest') + $loan->repayment_schedules->sum('fees') + $loan->repayment_schedules->sum('penalties');
            $body = str_replace('{{loan_principal}}', number_format($loan->principal, $loan->decimals), $body);
            $body = str_replace('{{loan_arrears_amount}}', number_format($arrears_amount, $loan->decimals), $body);
            $body = str_replace('{{loan_arrears_days}}', $arrears_days, $body);
            $body = str_replace('{{loan_disbursed_on_date}}', $loan->disbursed_on_date, $body);
            $body = str_replace('{{loan_expected_disbursement_date}}', $loan->expected_disbursement_date, $body);
            $body = str_replace('{{loan_first_payment_date}}', $loan->first_payment_date, $body);
            $body = str_replace('{{loan_interest_rate}}', $loan->interest_rate, $body);
            $body = str_replace('{{loan_approved_on_date}}', $loan->approved_on_date, $body);
            $body = str_replace('{{loan_rejected_on_date}}', $loan->rejected_on_date, $body);
            $body = str_replace('{{loan_written_off_on_date}}', $loan->written_off_on_date, $body);
            $body = str_replace('{{loan_disbursed_notes}}', $loan->disbursed_notes, $body);
            $body = str_replace('{{loan_approved_notes}}', $loan->approved_notes, $body);
            $body = str_replace('{{loan_written_off_notes}}', $loan->written_off_notes, $body);
            $body = str_replace('{{loan_closed_on_date}}', $loan->closed_on_date, $body);
            $body = str_replace('{{loan_closed_notes}}', $loan->closed_notes, $body);
            $body = str_replace('{{loan_rescheduled_on_date}}', $loan->rescheduled_on_date, $body);
            $body = str_replace('{{loan_rescheduled_notes}}', $loan->rescheduled_notes, $body);
            $body = str_replace('{{loan_withdrawn_on_date}', $loan->withdrawn_on_date, $body);
            $body = str_replace('{{loan_withdrawn_notes}}', $loan->withdrawn_notes, $body);
            $body = str_replace('{{loan_external_id}}', $loan->external_id, $body);
            $body = str_replace('{{loan_account_number}}', $loan->account_number, $body);
            $body = str_replace('{{loan_approved_amount}}', $loan->approved_amount, $body);
            $body = str_replace('{{loan_applied_amount}}', $loan->applied_amount, $body);
            $body = str_replace('{{loan_submitted_on_date}}', $loan->submitted_on_date, $body);
            $body = str_replace('{{loan_balance}}', number_format($balance, $loan->decimals), $body);
            $body = str_replace('{{loan_total_interest}}', number_format($loan->repayment_schedules->sum('interest'), $loan->decimals), $body);
            $body = str_replace('{{loan_total_fees}}', number_format($loan->repayment_schedules->sum('fees'), $loan->decimals), $body);
            $body = str_replace('{{loan_total_penalties}}', number_format($loan->repayment_schedules->sum('penalties'), $loan->decimals), $body);
            $body = str_replace('{{loan_outstanding_interest}}', number_format($interest, $loan->decimals), $body);
            $body = str_replace('{{loan_outstanding_fees}}', number_format($fees, $loan->decimals), $body);
            $body = str_replace('{{loan_outstanding_penalties}}', number_format($penalties, $loan->decimals), $body);
            $body = str_replace('{{loan_outstanding_principal}}', number_format($principal, $loan->decimals), $body);
            $body = str_replace('{{loan_total_payments}}', number_format($total_due - $balance, $loan->decimals), $body);
            $body = str_replace('{{loan_total_due}}', number_format($total_due, $loan->decimals), $body);
            $body = str_replace('{{loan_term}}', $loan->loan_term, $body);
        }
        //loan transaction
        if (array_key_exists('loan_transaction_id', $args)) {
            $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->with('created_by')->find($args['loan_transaction_id']);
            (empty($loan_transaction->loan)) ? $decimals = 2 : $decimals = $loan_transaction->loan->decimals;
            $body = str_replace('{{loan_transaction_amount}}', number_format($loan_transaction->amount, $decimals), $body);
            $body = str_replace('{{loan_transaction_credit}}', number_format($loan_transaction->credit, $decimals), $body);
            $body = str_replace('{{loan_transaction_debit}}', number_format($loan_transaction->debit, $decimals), $body);
            $body = str_replace('{{loan_transaction_submitted_on}}', $loan_transaction->submitted_on, $body);
            $body = str_replace('{{loan_transaction_created_on}}', $loan_transaction->created_on, $body);
            if (!empty($loan_transaction->payment_detail)) {
                if (!empty($loan_transaction->payment_detail->type)) {
                    $body = str_replace('{{loan_transaction_payment_type}}', $loan_transaction->payment_detail->type->name, $body);
                }
                $body = str_replace('{{loan_transaction_receipt_number}}', $loan_transaction->payment_detail->receipt, $body);
                $body = str_replace('{{loan_transaction_cheque_number}}', $loan_transaction->payment_detail->cheque_number, $body);
                $body = str_replace('{{loan_transaction_account_number}', $loan_transaction->payment_detail->account_number, $body);
                $body = str_replace('{{loan_transaction_bank_name}}', $loan_transaction->payment_detail->bank_name, $body);
                $body = str_replace('{{loan_transaction_routing_code}}', $loan_transaction->payment_detail->routing_code, $body);
                $body = str_replace('{{loan_transaction_description}', $loan_transaction->payment_detail->description, $body);
            }
            if (!empty($loan_transaction->created_by)) {
                $body = str_replace('{{loan_transaction_created_by}}', $loan_transaction->created_by->first_name . ' ' . $loan_transaction->created_by->last_name, $body);
            }
        }
        //loan repayment schedule
        if (array_key_exists('loan_repayment_schedule_id', $args)) {
            $loan_repayment_schedule = LoanRepaymentSchedule::with('loan')->find($args['loan_repayment_schedule_id']);
            (empty($loan_repayment_schedule->loan)) ? $decimals = 2 : $decimals = $loan_repayment_schedule->loan->decimals;
            $body = str_replace('{{loan_repayment_schedule_amount}}', number_format($loan_repayment_schedule->total_due, $decimals), $body);
            $body = str_replace('{{loan_repayment_schedule_due_date}}', $loan_repayment_schedule->due_date, $body);
        }
        //savings
        if (array_key_exists('savings_id', $args)) {
            $savings =Savings::with('transactions')->with('savings_product')->with('branch')->with('savings_officer')->with('currency')->find($args['savings_id']);
            $body = str_replace('{{savings_id}}', $savings->id, $body);
            if ($savings->status == 'pending') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.pending', 1) . ' ' . trans_choice('general.approval', 1), $body);
            }
            if ($savings->status == 'submitted') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.pending_approval', 1), $body);
            }
            if ($savings->status == 'dormant') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.dormant', 1), $body);
            }
            if ($savings->status == 'approved') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.awaiting_activation', 1), $body);
            }
            if ($savings->status == 'active') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.active', 1), $body);
            }
            if ($savings->status == 'rejected') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.rejected', 1), $body);
            }
            if ($savings->status == 'withdrawn') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.withdrawn', 1), $body);
            }
            if ($savings->status == 'inactive') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.inactive', 1), $body);
            }
            if ($savings->status == 'closed') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.closed', 1), $body);
            }
            if ($savings->status == 'pending_reschedule') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.pending_reschedule', 1), $body);
            }
            if ($savings->status == 'rescheduled') {
                $body = str_replace('{{savings_status}}', trans_choice('savings::general.rescheduled', 1), $body);
            }
            if (!empty($savings->savings_purpose)) {
                $body = str_replace('{{savings_purpose}}', $savings->savings_purpose->name, $body);
            }
            if (!empty($savings->fund)) {
                $body = str_replace('{{savings_fund}}', $savings->fund->name, $body);
            }
            if (!empty($savings->currency)) {
                $body = str_replace('{{savings_currency_name}}', $savings->currency->name, $body);
                $body = str_replace('{{savings_currency_symbol}}', $savings->currency->symbol, $body);
            }
            if (!empty($savings->branch)) {
                $body = str_replace('{{savings_branch}}', $savings->branch->name, $body);
            }
            if (!empty($savings->savings_officer)) {
                $body = str_replace('{{savings_officer}}', $savings->savings_officer->first_name . ' ' . $savings->savings_officer->last_name, $body);
            }
            $balance = $savings->transactions->sum('credit') - $savings->transactions->sum('debit');
            $body = str_replace('{{savings_balance}}', number_format($balance, $savings->decimals), $body);
            $body = str_replace('{{savings_interest_rate}}', $savings->interest_rate, $body);
            $body = str_replace('{{savings_approved_on_date}}', $savings->approved_on_date, $body);
            $body = str_replace('{{savings_rejected_on_date}}', $savings->rejected_on_date, $body);
            $body = str_replace('{{savings_inactive_on_date}}', $savings->inactive_on_date, $body);
            $body = str_replace('{{savings_inactive_notes}}', $savings->inactive_notes, $body);
            $body = str_replace('{{savings_activated_on_date}}', $savings->activated_on_date, $body);
            $body = str_replace('{{savings_activated_notes}}', $savings->activated_notes, $body);
            $body = str_replace('{{savings_approved_notes}}', $savings->approved_notes, $body);
            $body = str_replace('{{savings_dormant_notes}}', $savings->dormant_notes, $body);
            $body = str_replace('{{savings_closed_on_date}}', $savings->closed_on_date, $body);
            $body = str_replace('{{savings_closed_notes}}', $savings->closed_notes, $body);

        }
        //savings transaction
        if (array_key_exists('savings_transaction_id', $args)) {
            $savings_transaction = SavingsTransaction::with('payment_detail')->with('savings')->with('created_by')->find($args['savings_transaction_id']);
            (empty($savings_transaction->savings)) ? $decimals = 2 : $decimals = $savings_transaction->savings->decimals;
            $body = str_replace('{{savings_transaction_amount}}', number_format($savings_transaction->amount, $decimals), $body);
            $body = str_replace('{{savings_transaction_credit}}', number_format($savings_transaction->credit, $decimals), $body);
            $body = str_replace('{{savings_transaction_debit}}', number_format($savings_transaction->debit, $decimals), $body);
            $body = str_replace('{{savings_transaction_submitted_on}}', $savings_transaction->submitted_on, $body);
            $body = str_replace('{{savings_transaction_created_on}}', $savings_transaction->created_on, $body);
            if (!empty($savings_transaction->payment_detail)) {
                if (!empty($savings_transaction->payment_detail->type)) {
                    $body = str_replace('{{savings_transaction_payment_type}}', $savings_transaction->payment_detail->type->name, $body);
                }
                $body = str_replace('{{savings_transaction_receipt_number}}', $savings_transaction->payment_detail->receipt, $body);
                $body = str_replace('{{savings_transaction_cheque_number}}', $savings_transaction->payment_detail->cheque_number, $body);
                $body = str_replace('{{savings_transaction_account_number}', $savings_transaction->payment_detail->account_number, $body);
                $body = str_replace('{{savings_transaction_bank_name}}', $savings_transaction->payment_detail->bank_name, $body);
                $body = str_replace('{{savings_transaction_routing_code}}', $savings_transaction->payment_detail->routing_code, $body);
                $body = str_replace('{{savings_transaction_description}', $savings_transaction->payment_detail->description, $body);
            }
            if (!empty($savings_transaction->created_by)) {
                $body = str_replace('{{savings_transaction_created_by}}', $savings_transaction->created_by->first_name . ' ' . $savings_transaction->created_by->last_name, $body);
            }
        }
        return $body;
    }
}
if (!function_exists('log_campaign')) {

    /**
     * Logs a communication campaign
     * @param array $args
     */
    function log_campaign(array $args)
    {
        $communication_campaign_log = new CommunicationCampaignLog();
        foreach ($args as $key => $value) {
            $communication_campaign_log->$key = $value;
        }
        $communication_campaign_log->save();

    }
}

