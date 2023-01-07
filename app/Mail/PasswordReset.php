<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;
    public $user;


    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $reminder = Reminder::exists($this->user) ?: Reminder::create($this->user);
        $code = $reminder->code;
        $body = Setting::where('setting_key', 'password_reset_template')->first()->setting_value;
        $body = str_replace('{firstName}', $this->user->first_name, $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $body = str_replace('{lastName}', $this->user->last_name, $body);
        $body = str_replace('{resetLink}', url('confirm_password_reset/' . $this->user->id . '/' . $code), $body);
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'password_reset_subject')->first()->setting_value)
            ->view('emails.basic_base', compact('user', 'body'));
    }
}
