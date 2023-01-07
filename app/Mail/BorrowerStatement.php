<?php

namespace App\Mail;

use App\Helpers\GeneralHelper;
use App\Models\Borrower;
use App\Models\Email;
use App\Models\Loan;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

class BorrowerStatement extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $borrower;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $borrower = $this->borrower;
        $loan = $borrower->loan;
        $body = Setting::where('setting_key',
            'borrower_statement_email_template')->first()->setting_value;
        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
        $body = str_replace('{loansPayments}', GeneralHelper::borrower_loans_total_paid($borrower->id), $body);
        $body = str_replace('{loansDue}',
            round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
        $body = str_replace('{loansBalance}',
            round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                2), $body);
        $body = str_replace('{loanPayments}',
            GeneralHelper::borrower_loans_total_paid($borrower->id),
            $body);
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        $file_name = $borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Client Statement.pdf";
        $mail = new Email();
        $mail->user_id = Sentinel::getUser()->id;
        $mail->message = $body;
        $mail->subject = Setting::where('setting_key',
            'borrower_statement_email_subject')->first()->setting_value;
        $mail->recipients = 1;
        $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
        $mail->save();
        $pdf = PDF::loadView('loan.pdf_borrower_statement', compact('loans'));
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'borrower_statement_email_subject')->first()->setting_value)
            ->view('emails.basic_base', compact('body'))->attachData($pdf->output(),
                trans_choice('general.loan', 1) . ' ' . trans_choice('general.statement', 1) . ".pdf",
                ['mime' => 'application/pdf']);
    }
}
