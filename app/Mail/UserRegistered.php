<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $body = Setting::where('setting_key', 'login_details_email_template')->first()->setting_value;
        $body = str_replace('{name}', $this->user->first_name.' '.$this->user->last_name, $body);
        $body = str_replace('{email}', $this->user->email, $body);
        $body = str_replace('{password}', $this->password, $body);
        $body = str_replace('{loginUrl}', url('/'), $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'login_details_email_subject')->first()->setting_value)
            ->view('emails.basic_base', compact('body'));
    }
}
