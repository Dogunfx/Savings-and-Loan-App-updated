<?php

namespace App\Console\Commands;

use App\Helpers\GeneralHelper;
use App\Models\Email;
use App\Models\LoanSchedule;
use App\Models\Setting;
use App\Models\Sms;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProcessReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails and sms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //check repayment reminders
        if (Setting::where('setting_key', 'auto_repayment_email_reminder')->first()->setting_value == 1) {
            $days = Setting::where('setting_key', 'auto_repayment_days')->first()->setting_value;
            $due_date = Carbon::now()->addDays($days)->format("Y-m-d");
            foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
                if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

                    //check if borrower has email
                    if (!empty($schedule->email)) {
                        $body = Setting::where('setting_key',
                            'loan_payment_reminder_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $schedule->title, $body);
                        $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $schedule->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
                        $body = str_replace('{borrowerEmail}', $schedule->email, $body);
                        $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
                        $body = str_replace('{paymentAmount}',
                            round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
                                2), $body);
                        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                        $body = str_replace('{loanPayments}', $schedule->payments, $body);
                        $body = str_replace('{loanDue}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2), $body);
                        $body = str_replace('{loanBalance}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
                                2), $body);
                        Mail::send([], [], function ($message) use ($schedule, $body) {
                            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to($schedule->email);
                            $headers = $message->getHeaders();
                            $message->setContentType('text/html');
                            $message->setBody($body);
                            $message->setSubject(Setting::where('setting_key',
                                'loan_payment_reminder_subject')->first()->setting_value);
                        });
                        $mail = new Email();
                        //$mail->user_id = Sentinel::getUser()->id;
                        $mail->message = $body;
                        $mail->subject = Setting::where('setting_key',
                            'loan_payment_reminder_subject')->first()->setting_value;
                        $mail->recipients = 1;
                        $mail->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
                        $mail->save();
                    }
                }
            }
        }
        if (Setting::where('setting_key', 'auto_repayment_sms_reminder')->first()->setting_value == 1) {
            $days = Setting::where('setting_key', 'auto_repayment_days')->first()->setting_value;
            $due_date = Carbon::now()->addDays($days)->format("Y-m-d");
            foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
                if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

                    //check if borrower has mobile
                    if (!empty($schedule->mobile)) {
                        $body = Setting::where('setting_key',
                            'loan_payment_reminder_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $schedule->title, $body);
                        $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $schedule->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
                        $body = str_replace('{borrowerEmail}', $schedule->email, $body);
                        $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
                        $body = str_replace('{paymentAmount}',
                            round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
                                2), $body);
                        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                        $body = str_replace('{loanPayments}', $schedule->payments, $body);
                        $body = str_replace('{loanDue}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2), $body);
                        $body = str_replace('{loanBalance}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
                                2), $body);
                        $body = strip_tags($body);
                        $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                        GeneralHelper::send_sms($schedule->mobile, $body);
                        $sms = new Sms();
                        //$sms->user_id = Sentinel::getUser()->id;
                        $sms->message = $body;
                        $sms->gateway = $active_sms;
                        $sms->recipients = 1;
                        $sms->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
                        $sms->save();
                    }
                }
            }
        }
        //check for missed repayments
        if (Setting::where('setting_key', 'auto_overdue_repayment_email_reminder')->first()->setting_value == 1) {
            $days = Setting::where('setting_key', 'auto_overdue_repayment_days')->first()->setting_value;
            $due_date = Carbon::now()->subDays($days)->format("Y-m-d");
            foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
                if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

                    //check if borrower has email
                    if (!empty($schedule->email)) {
                        $body = Setting::where('setting_key',
                            'missed_payment_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $schedule->title, $body);
                        $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $schedule->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
                        $body = str_replace('{borrowerEmail}', $schedule->email, $body);
                        $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
                        $body = str_replace('{paymentAmount}',
                            round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
                                2), $body);
                        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                        $body = str_replace('{loanPayments}', $schedule->payments, $body);
                        $body = str_replace('{loanDue}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2), $body);
                        $body = str_replace('{loanBalance}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
                                2), $body);
                        Mail::send([], [], function ($message) use ($schedule, $body) {
                            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to($schedule->email);
                            $headers = $message->getHeaders();
                            $message->setContentType('text/html');
                            $message->setBody($body);
                            $message->setSubject(Setting::where('setting_key',
                                'missed_payment_email_subject')->first()->setting_value);
                        });
                        $mail = new Email();
                        //$mail->user_id = Sentinel::getUser()->id;
                        $mail->message = $body;
                        $mail->subject = Setting::where('setting_key',
                            'missed_payment_email_subject')->first()->setting_value;
                        $mail->recipients = 1;
                        $mail->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
                        $mail->save();
                    }
                }
            }
        }
        if (Setting::where('setting_key', 'auto_overdue_repayment_sms_reminder')->first()->setting_value == 1) {
            $days = Setting::where('setting_key', 'auto_overdue_repayment_days')->first()->setting_value;
            $due_date = Carbon::now()->subDays($days)->format("Y-m-d");
            foreach (DB::table("loan_schedules")->leftJoin("borrowers", "borrowers.id", "loan_schedules.borrower_id")->selectRaw(DB::raw("borrowers.*,loan_schedules.*,(SELECT SUM(principal) FROM loan_schedules) total_principal,(SELECT SUM(interest)  FROM loan_schedules) total_interest,(SELECT SUM(fees)  FROM loan_schedules) total_fees,(SELECT SUM(penalty)  FROM loan_schedules) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loan_schedules.loan_id) payments"))->where('loan_schedules.due_date', $due_date)->get() as $schedule) {
                if (($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived) > ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid)) {

                    //check if borrower has email
                    if (!empty($schedule->mobile)) {
                        $body = Setting::where('setting_key',
                            'missed_payment_sms_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $schedule->title, $body);
                        $body = str_replace('{borrowerFirstName}', $schedule->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $schedule->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $schedule->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $schedule->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $schedule->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $schedule->phone, $body);
                        $body = str_replace('{borrowerEmail}', $schedule->email, $body);
                        $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
                        $body = str_replace('{paymentAmount}',
                            round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - ($schedule->principal_paid + $schedule->interest_paid + $schedule->penalty_paid + $schedule->fees_paid),
                                2), $body);
                        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                        $body = str_replace('{loanPayments}', $schedule->payments, $body);
                        $body = str_replace('{loanDue}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived, 2), $body);
                        $body = str_replace('{loanBalance}',
                            round($schedule->total_principal - $schedule->total_principal_waived + $schedule->total_interest - $schedule->total_interest_waived + $schedule->total_fees - $schedule->total_fees_waived + $schedule->total_penalty - $schedule->total_penalty_waived - $schedule->payments,
                                2), $body);
                        $body = strip_tags($body);
                        $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                        GeneralHelper::send_sms($schedule->mobile, $body);
                        $sms = new Sms();
                        //$sms->user_id = Sentinel::getUser()->id;
                        $sms->message = $body;
                        $sms->gateway = $active_sms;
                        $sms->recipients = 1;
                        $sms->send_to = $schedule->first_name . ' ' . $schedule->last_name . '(' . $schedule->unique_number . ')';
                        $sms->save();
                    }
                }
            }
        }
    }
}
