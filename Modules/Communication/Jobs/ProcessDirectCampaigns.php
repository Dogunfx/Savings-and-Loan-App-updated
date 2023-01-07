<?php

namespace Modules\Communication\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Client\Entities\Client;
use Modules\Communication\Emails\SendBasicEmail;
use Modules\Communication\Entities\CommunicationCampaign;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Setting\Entities\Setting;

class ProcessDirectCampaigns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $communication_campaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CommunicationCampaign $communication_campaign)
    {
        $this->communication_campaign = $communication_campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $communication_campaign = $this->communication_campaign;
        $branch_id = $communication_campaign->branch_id;
        $loan_officer_id = $communication_campaign->loan_officer_id;
        $loan_product_id = $communication_campaign->loan_product_id;
        $attachment_type = $communication_campaign->communication_campaign_attachment_type_id;
        $from_x = $communication_campaign->from_x;
        $to_y = $communication_campaign->to_y;
        $cycle_x = $communication_campaign->cycle_x;
        $cycle_y = $communication_campaign->cycle_y;
        $overdue_x = $communication_campaign->overdue_x;
        $overdue_y = $communication_campaign->overdue_y;
        //active clients
        if ($communication_campaign->communication_campaign_business_rule_id == 1) {
            $clients = Client::where('status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loan_officer_id', $loan_officer_id);
            })->get();
            foreach ($clients as $client) {
                if ($communication_campaign->campaign_type == 'sms') {
                    if (!empty($client->mobile)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "client_id" => $client->id]);
                        send_sms($client->mobile, $description, $communication_campaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'client_id' => $client->id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $client->mobile,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
                if ($communication_campaign->campaign_type == 'email') {
                    if (!empty($client->email)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "client_id" => $client->id]);
                        $email = $client->email;
                        $subject = $communication_campaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'client_id' => $client->id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $client->email,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
            }
        }
        //active clients who have never had a loan
        if ($communication_campaign->communication_campaign_business_rule_id == 2) {
            $clients = Client::leftJoin("loans", "clients.id", "loans.client_id")->where('clients.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('clients.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('clients.loan_officer_id', $loan_officer_id);
            })->selectRaw("clients.*,count(loans.id) loan_count")->having('loan_count', 0)->get();
            foreach ($clients as $client) {
                if ($communication_campaign->campaign_type == 'sms') {
                    if (!empty($client->mobile)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "client_id" => $client->id]);
                        send_sms($client->mobile, $description, $communication_campaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'client_id' => $client->id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $client->mobile,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
                if ($communication_campaign->campaign_type == 'email') {
                    if (!empty($client->email)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "client_id" => $client->id]);
                        $email = $client->email;
                        $subject = $communication_campaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'client_id' => $client->id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $client->email,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
            }
        }
        //all clients with an outstanding loan
        if ($communication_campaign->communication_campaign_business_rule_id == 3) {
            $clients = Client::leftJoin("loans", "clients.id", "loans.client_id")->where('clients.status', 'active')->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('clients.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('clients.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->selectRaw("clients.*,count(loans.id) loan_count")->having('loan_count', '>', 0)->get();
            foreach ($clients as $client) {
                if ($communication_campaign->campaign_type == 'sms') {
                    if (!empty($client->mobile)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "client_id" => $client->id]);
                        send_sms($client->mobile, $description, $communication_campaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'client_id' => $client->id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $client->mobile,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
                if ($communication_campaign->campaign_type == 'email') {
                    if (!empty($client->email)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "client_id" => $client->id]);
                        $email = $client->email;
                        $subject = $communication_campaign->subject;
                        Mail::to($email)->send(new SendBasicEmail($subject, $description));
                        //log sms
                        log_campaign([
                            'client_id' => $client->id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $client->email,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
            }
        }
        //all clients with loans in arrears
        if ($communication_campaign->communication_campaign_business_rule_id == 4) {
            $loans = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")->leftJoin("clients", "clients.id", "loans.client_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loans.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->when($from_x, function ($query) use ($from_x, $to_y) {
                $query->havingRaw("days_in_arrears between $from_x AND $to_y");
            })->whereRaw("loan_repayment_schedules.id =(select lrs.id from loan_repayment_schedules as lrs where lrs.due_date<now() AND lrs.loan_id=loan_repayment_schedules.loan_id AND lrs.total_due > 0 order by due_date desc limit 1)")->selectRaw("loans.client_id,loans.id,clients.mobile,clients.email,datediff(now(),loan_repayment_schedules.due_date) days_in_arrears")->get();
            foreach ($loans as $loan) {
                if ($communication_campaign->campaign_type == 'sms') {
                    if (!empty($loan->mobile)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                        send_sms($loan->mobile, $description, $communication_campaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'client_id' => $loan->client_id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->mobile,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
                if ($communication_campaign->campaign_type == 'email') {
                    if (!empty($loan->email)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                        $email = $loan->email;
                        $subject = $communication_campaign->subject;
                        $attachments=[];
                        if ($attachment_type == '1') {
                            //loan schedule
                            $loan = Loan::find($loan->id);
                            $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $attachments[] =
                                [
                                    "file_path" => $pdf->output(),
                                    "file_name" => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                    "extra_args" => ['mime' => 'application/pdf']
                                ]
                            ;
                        }
                        Mail::to($email)->send(new SendBasicEmail($subject, $description,$attachments));
                        //log sms
                        log_campaign([
                            'client_id' => $loan->client_id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->email,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
            }
        }
        //loans disbursed to clients
        if ($communication_campaign->communication_campaign_business_rule_id == 5) {
            $loans = Loan::join("clients", "clients.id", "loans.client_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loans.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->whereBetween('disbursed_on_date', [Carbon::today()->subDays($to_y)->format("Y-m-d"), Carbon::today()->subDays($from_x)->format("Y-m-d")])->selectRaw("loans.client_id,loans.id,clients.mobile,clients.email")->get();
            foreach ($loans as $loan) {
                if ($communication_campaign->campaign_type == 'sms') {
                    if (!empty($loan->mobile)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                        send_sms($loan->mobile, $description, $communication_campaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'client_id' => $loan->client_id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->mobile,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
                if ($communication_campaign->campaign_type == 'email') {
                    if (!empty($loan->email)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                        $email = $loan->email;
                        $subject = $communication_campaign->subject;
                        $attachments=[];
                        if ($attachment_type == '1') {
                            //loan schedule
                            $loan = Loan::find($loan->id);
                            $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $attachments[] =
                                [
                                    "file_path" => $pdf->output(),
                                    "file_name" => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                    "extra_args" => ['mime' => 'application/pdf']
                                ]
                            ;
                        }
                        Mail::to($email)->send(new SendBasicEmail($subject, $description,$attachments));

                        //log sms
                        log_campaign([
                            'client_id' => $loan->client_id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->email,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
            }
        }
        //loan payments due
        if ($communication_campaign->communication_campaign_business_rule_id == 6) {
            $loans = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")->join("clients", "clients.id", "loans.client_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where('loans.loan_officer_id', $loan_officer_id);
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where('loans.loan_product_id', $loan_product_id);
            })->where('loan_repayment_schedules.total_due', '>', 0)->whereBetween('loan_repayment_schedules.due_date', [Carbon::today()->addDays($from_x)->format("Y-m-d"), Carbon::today()->addDays($to_y)->format("Y-m-d")])->selectRaw("loans.client_id,loans.id,clients.mobile,clients.email,loan_repayment_schedules.id loan_repayment_schedule_id")->get();
            foreach ($loans as $loan) {
                if ($communication_campaign->campaign_type == 'sms') {
                    if (!empty($loan->mobile)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "loan_id" => $loan->id, "client_id" => $loan->client_id, "loan_repayment_schedule_id" => $loan->loan_repayment_schedule_id]);
                        send_sms($loan->mobile, $description, $communication_campaign->sms_gateway_id);
                        //log sms
                        log_campaign([
                            'client_id' => $loan->client_id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->mobile,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
                if ($communication_campaign->campaign_type == 'email') {
                    if (!empty($loan->email)) {
                        $description = template_replace_tags(["body" => $communication_campaign->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                        $email = $loan->email;
                        $subject = $communication_campaign->subject;
                        $attachments=[];
                        if ($attachment_type == '1') {
                            //loan schedule
                            $loan = Loan::find($loan->id);
                            $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                            $attachments[] =
                                [
                                    "file_path" => $pdf->output(),
                                    "file_name" => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                    "extra_args" => ['mime' => 'application/pdf']
                                ]
                            ;
                        }
                        Mail::to($email)->send(new SendBasicEmail($subject, $description,$attachments));
                        //log sms
                        log_campaign([
                            'client_id' => $loan->client_id,
                            'communication_campaign_id' => $communication_campaign->id,
                            'campaign_type' => $communication_campaign->campaign_type,
                            'description' => $description,
                            'send_to' => $loan->email,
                            'status' => 'sent',
                            'campaign_name' => $communication_campaign->name
                        ]);
                    }
                }
            }
        }
        $communication_campaign->status = 'done';
        $communication_campaign->save();
    }
}
