<?php

namespace App\Mail;

use App\Helpers\GeneralHelper;
use App\Models\Email;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

class LoanStatement extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $loan = $this->loan;
        $borrower = $loan->borrower;
        $body = Setting::where('setting_key',
            'loan_statement_email_template')->first()->setting_value;
        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
        $body = str_replace('{loanNumber}', $loan->id, $body);
        $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
        $body = str_replace('{loanDue}',
            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
        $body = str_replace('{loanBalance}',
            round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                2), $body);
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        $file_name = $borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Loan Statement.pdf";
        $mail = new Email();
        $mail->user_id = Sentinel::getUser()->id;
        $mail->message = $body;
        $mail->subject = Setting::where('setting_key',
            'loan_statement_email_subject')->first()->setting_value;
        $mail->recipients = 1;
        $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
        $mail->save();
        $pdf = PDF::loadView('loan.pdf_loan_statement', compact('loan', 'payments'));
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'loan_schedule_email_subject')->first()->setting_value)
            ->view('emails.basic_base', compact('body'))->attachData($pdf->output(),
                trans_choice('general.loan', 1) . ' ' . trans_choice('general.statement', 1) . ".pdf",
                ['mime' => 'application/pdf']);
    }
}
