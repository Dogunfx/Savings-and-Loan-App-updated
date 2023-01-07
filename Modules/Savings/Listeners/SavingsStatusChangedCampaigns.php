<?php

namespace Modules\Savings\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\Communication\Entities\CommunicationCampaign;
use Modules\Setting\Entities\Setting;
use PDF;

class SavingsStatusChangedCampaigns implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $savings = $event->savings;
        $previous_status = $event->previous_status;
        $communication_campaign_business_rule_id = '';

        if ($savings->status == 'submitted' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 22;
        }
        if ($savings->status == 'pending' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 22;
        }
        if ($savings->status == 'rejected' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 23;
        }
        if ($savings->status == 'approved' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 24;
        }
        if ($savings->status == 'active' && $previous_status=='approved') {
            $communication_campaign_business_rule_id = 25;
        }
        if ($savings->status == 'dormant') {
            $communication_campaign_business_rule_id = 26;
        }
        if ($savings->status == 'inactive') {
            $communication_campaign_business_rule_id = 27;
        }
        if ($savings->status == 'closed' && $previous_status == 'active') {
            $communication_campaign_business_rule_id = 28;
        }

        $campaigns = CommunicationCampaign::where('trigger_type', 'triggered')->where('status', 'active')->where('savings_product_id', $savings->savings_product_id)->where('communication_campaign_business_rule_id', $communication_campaign_business_rule_id)->get();
        foreach ($campaigns as $key) {
            if ($key->campaign_type == 'sms') {
                if (!empty($savings->client->mobile)) {
                    $description = template_replace_tags(["body" => $key->description, "client_id" => $savings->client_id, "savings_id" => $savings->id]);
                    send_sms($savings->client->mobile, $description, $key->sms_gateway_id);
                    //log sms
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $savings->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $savings->client->mobile,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
            if ($key->campaign_type == 'email') {
                if (!empty($savings->client->email)) {
                    $description = template_replace_tags(["body" => $key->description, "client_id" => $savings->client_id, "savings_id" => $savings->id]);
                    $email = $savings->client->email;
                    $subject = $key->subject;
                    $attachment_type = $key->communication_campaign_attachment_type_id;
                    Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $savings) {
                        $message->subject($subject);
                        $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                        $message->to($email);
                        if ($attachment_type == '3') {
                            //savings schedule
                            $pdf = PDF::loadView('savings::savings_statement.pdf', compact('savings'))->setPaper('a4', 'landscape');
                            $message->attachData($pdf->output(),
                                trans_choice('savings::general.savings', 1) . ' ' . trans_choice('savings::general.statement', 1) . ".pdf",
                                ['mime' => 'application/pdf']);
                        }
                    });
                    //log sms
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $savings->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $savings->client->email,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
        }
    }
}
