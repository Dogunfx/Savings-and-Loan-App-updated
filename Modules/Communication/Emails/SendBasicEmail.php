<?php

namespace Modules\Communication\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Setting\Entities\Setting;

class SendBasicEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $body;
    public $subject;
    public $attachments;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $attachments = [])
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->attachments = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $body = $this->body;
        $subject = $this->subject;
        $attachments = $this->attachments;
        $email = $this->markdown('communication::emails.basic_email', compact('body'))
            ->subject($subject)
            ->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
        foreach ($attachments as $key) {
            $email->attachData($key['file_path'],$key['file_name'],$key['extra_args']);
        }
        return $email;
    }
}
