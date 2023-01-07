<?php

namespace Modules\Loan\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Communication\Entities\CommunicationCampaign;
use Illuminate\Support\Facades\Mail;
use Modules\Setting\Entities\Setting;
use PDF;

class LoanStatusChangedCampaigns implements ShouldQueue
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
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $loan = $event->loan;
        $previous_status = $event->previous_status;
        $communication_campaign_business_rule_id = '';

        if ($loan->status == 'submitted' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 15;
        }
        if ($loan->status == 'pending' && empty($previous_status)) {
            $communication_campaign_business_rule_id = 15;
        }
        if ($loan->status == 'rejected' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 16;
        }
        if ($loan->status == 'approved' && $previous_status == 'submitted') {
            $communication_campaign_business_rule_id = 17;
        }
        if ($loan->status == 'active' && $previous_status=='approved') {
            $communication_campaign_business_rule_id = 18;
        }
        if ($loan->status == 'rescheduled') {
            $communication_campaign_business_rule_id = 19;
        }
        if ($loan->status == 'closed' && $previous_status == 'active') {
            $communication_campaign_business_rule_id = 20;
        }

        $campaigns = CommunicationCampaign::where('trigger_type', 'triggered')->where('status', 'active')->where('loan_product_id', $loan->loan_product_id)->where('communication_campaign_business_rule_id', $communication_campaign_business_rule_id)->get();
        foreach ($campaigns as $key) {
            if ($key->campaign_type == 'sms') {
                if (!empty($loan->client->mobile)) {
                    $description = template_replace_tags(["body" => $key->description, "client_id" => $loan->client_id, "loan_id" => $loan->id]);
                    send_sms($loan->client->mobile, $description, $key->sms_gateway_id);
                    //log sms
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $loan->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $loan->client->mobile,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
            if ($key->campaign_type == 'email') {
                if (!empty($loan->client->email)) {
                    $description = template_replace_tags(["body" => $key->description, "client_id" => $loan->client_id, "loan_id" => $loan->id]);
                    $email = $loan->client->email;
                    $subject = $key->subject;
                    $attachment_type = $key->communication_campaign_attachment_type_id;
                    Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $loan) {
                        $message->subject($subject);
                        $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                        $message->to($email);
                        if ($attachment_type == '1') {
                            //loan schedule
                            $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $message->attachData($pdf->output(),
                                trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                ['mime' => 'application/pdf']);
                        }
                    });
                    //log sms
                    log_campaign([
                        'created_by_id' => Auth::id(),
                        'client_id' => $loan->client_id,
                        'communication_campaign_id' => $key->id,
                        'campaign_type' => $key->campaign_type,
                        'description' => $description,
                        'send_to' => $loan->client->email,
                        'status' => 'sent',
                        'campaign_name' => $key->name
                    ]);
                }
            }
        }
    }
}
