<?php

namespace Modules\Communication\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Client\Entities\Client;
use Modules\Communication\Entities\CommunicationCampaign;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Setting\Entities\Setting;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PDF;

class ProcessScheduledCampaigns extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'campaigns:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes Scheduled Commands';

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
        $campaigns = CommunicationCampaign::where('trigger_type', 'schedule')->where('status', 'active')->where(function ($query) {
            $query->where(function ($query) {
                $query->where('scheduled_date', date("Y-m-d"));
                //$query->where('scheduled_time', date("H:i"));
            });
        })->get();
        foreach ($campaigns as $key) {
            $branch_id = $key->branch_id;
            $loan_officer_id = $key->loan_officer_id;
            $loan_product_id = $key->loan_product_id;
            $attachment_type = $key->communication_campaign_attachment_type_id;
            $from_x = $key->from_x;
            $to_y = $key->to_y;
            $cycle_x = $key->cycle_x;
            $cycle_y = $key->cycle_y;
            $overdue_x = $key->overdue_x;
            $overdue_y = $key->overdue_y;
            //active clients
            if ($key->communication_campaign_business_rule_id == 1) {
                $clients = Client::where('status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loan_officer_id', $loan_officer_id);
                })->get();
                foreach ($clients as $client) {
                    if ($key->campaign_type == 'sms') {
                        if (!empty($client->mobile)) {
                            $description = template_replace_tags(["body" => $key->description, "client_id" => $client->id]);
                            send_sms($client->mobile, $description, $key->sms_gateway_id);
                            //log sms
                            log_campaign([
                                'client_id' => $client->id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $client->mobile,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                    if ($key->campaign_type == 'email') {
                        if (!empty($client->email)) {
                            $description = template_replace_tags(["body" => $key->description, "client_id" => $client->id]);
                            $email = $client->email;
                            $subject = $key->subject;
                            Mail::send([], [], function ($message) use ($email, $description, $subject) {
                                $message->subject($subject);
                                $message->setBody($description);
                                $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                                $message->to($email);
                            });
                            //log sms
                            log_campaign([
                                'client_id' => $client->id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $client->email,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                }
            }
            //active clients who have never had a loan
            if ($key->communication_campaign_business_rule_id == 2) {
                $clients = Client::leftJoin("loans", "clients.id", "loans.client_id")->where('clients.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('clients.branch_id', $branch_id);
                })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('clients.loan_officer_id', $loan_officer_id);
                })->selectRaw("clients.*,count(loans.id) loan_count")->having('loan_count', 0)->get();
                foreach ($clients as $client) {
                    if ($key->campaign_type == 'sms') {
                        if (!empty($client->mobile)) {
                            $description = template_replace_tags(["body" => $key->description, "client_id" => $client->id]);
                            send_sms($client->mobile, $description, $key->sms_gateway_id);
                            //log sms
                            log_campaign([
                                'client_id' => $client->id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $client->mobile,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                    if ($key->campaign_type == 'email') {
                        if (!empty($client->email)) {
                            $description = template_replace_tags(["body" => $key->description, "client_id" => $client->id]);
                            $email = $client->email;
                            $subject = $key->subject;
                            Mail::send([], [], function ($message) use ($email, $description, $subject) {
                                $message->subject($subject);
                                $message->setBody($description);
                                $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                                $message->to($email);
                            });
                            //log sms
                            log_campaign([
                                'client_id' => $client->id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $client->email,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                }
            }
            //all clients with an outstanding loan
            if ($key->communication_campaign_business_rule_id == 3) {
                $clients = Client::leftJoin("loans", "clients.id", "loans.client_id")->where('clients.status', 'active')->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('clients.branch_id', $branch_id);
                })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('clients.loan_officer_id', $loan_officer_id);
                })->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })->selectRaw("clients.*,count(loans.id) loan_count")->having('loan_count', '>', 0)->get();
                foreach ($clients as $client) {
                    if ($key->campaign_type == 'sms') {
                        if (!empty($client->mobile)) {
                            $description = template_replace_tags(["body" => $key->description, "client_id" => $client->id]);
                            send_sms($client->mobile, $description, $key->sms_gateway_id);
                            //log sms
                            log_campaign([
                                'client_id' => $client->id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $client->mobile,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                    if ($key->campaign_type == 'email') {
                        if (!empty($client->email)) {
                            $description = template_replace_tags(["body" => $key->description, "client_id" => $client->id]);
                            $email = $client->email;
                            $subject = $key->subject;
                            Mail::send([], [], function ($message) use ($email, $description, $subject) {
                                $message->subject($subject);
                                $message->setBody($description);
                                $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                                $message->to($email);
                            });
                            //log sms
                            log_campaign([
                                'client_id' => $client->id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $client->email,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                }
            }
            //all clients with loans in arrears
            if ($key->communication_campaign_business_rule_id == 4) {
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
                    if ($key->campaign_type == 'sms') {
                        if (!empty($loan->mobile)) {
                            $description = template_replace_tags(["body" => $key->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                            send_sms($loan->mobile, $description, $key->sms_gateway_id);
                            //log sms
                            log_campaign([
                                'client_id' => $loan->client_id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $loan->mobile,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                    if ($key->campaign_type == 'email') {
                        if (!empty($loan->email)) {
                            $description = template_replace_tags(["body" => $key->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                            $email = $loan->email;
                            $subject = $key->subject;
                            Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $loan) {
                                $message->subject($subject);
                                $message->setBody($description);
                                $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                                $message->to($email);
                                if ($attachment_type == '1') {
                                    //loan schedule
                                    $loan = Loan::find($loan->id);
                                    $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                                    $message->attachData($pdf->output(),
                                        trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                        ['mime' => 'application/pdf']);
                                }
                            });
                            //log sms
                            log_campaign([
                                'client_id' => $loan->client_id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $loan->email,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                }
            }
            //loans disbursed to clients
            if ($key->communication_campaign_business_rule_id == 5) {
                $loans = Loan::join("clients", "clients.id", "loans.client_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })->whereBetween('disbursed_on_date', [Carbon::today()->subDays($to_y)->format("Y-m-d"), Carbon::today()->subDays($from_x)->format("Y-m-d")])->selectRaw("loans.client_id,loans.id,clients.mobile,clients.email")->get();
                foreach ($loans as $loan) {
                    if ($key->campaign_type == 'sms') {
                        if (!empty($loan->mobile)) {
                            $description = template_replace_tags(["body" => $key->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                            send_sms($loan->mobile, $description, $key->sms_gateway_id);
                            //log sms
                            log_campaign([
                                'client_id' => $loan->client_id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $loan->mobile,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                    if ($key->campaign_type == 'email') {
                        if (!empty($loan->email)) {
                            $description = template_replace_tags(["body" => $key->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                            $email = $loan->email;
                            $subject = $key->subject;
                            Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $loan) {
                                $message->subject($subject);
                                $message->setBody($description);
                                $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                                $message->to($email);
                                if ($attachment_type == '1') {
                                    //loan schedule
                                    $loan = Loan::find($loan->id);
                                    $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                                    $message->attachData($pdf->output(),
                                        trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                        ['mime' => 'application/pdf']);
                                }
                            });
                            //log sms
                            log_campaign([
                                'client_id' => $loan->client_id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $loan->email,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                }
            }
            //loan payments due
            if ($key->communication_campaign_business_rule_id == 6) {
                $loans = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")->join("clients", "clients.id", "loans.client_id")->where('loans.status', 'active')->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })->where('loan_repayment_schedules.total_due', '>', 0)->whereBetween('loan_repayment_schedules.due_date', [Carbon::today()->addDays($from_x)->format("Y-m-d"), Carbon::today()->addDays($to_y)->format("Y-m-d")])->selectRaw("loans.client_id,loans.id,clients.mobile,clients.email,loan_repayment_schedules.id loan_repayment_schedule_id")->get();
                foreach ($loans as $loan) {
                    if ($key->campaign_type == 'sms') {
                        if (!empty($loan->mobile)) {
                            $description = template_replace_tags(["body" => $key->description, "loan_id" => $loan->id, "client_id" => $loan->client_id, "loan_repayment_schedule_id" => $loan->loan_repayment_schedule_id]);
                            send_sms($loan->mobile, $description, $key->sms_gateway_id);
                            //log sms
                            log_campaign([
                                'client_id' => $loan->client_id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $loan->mobile,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                    if ($key->campaign_type == 'email') {
                        if (!empty($loan->email)) {
                            $description = template_replace_tags(["body" => $key->description, "loan_id" => $loan->id, "client_id" => $loan->client_id]);
                            $email = $loan->email;
                            $subject = $key->subject;
                            Mail::send([], [], function ($message) use ($email, $description, $subject, $attachment_type, $loan) {
                                $message->subject($subject);
                                $message->setBody($description);
                                $message->from(Setting::where('setting_key', 'core.company_email')->first()->setting_value, Setting::where('setting_key', 'core.company_name')->first()->setting_value);
                                $message->to($email);
                                if ($attachment_type == '1') {
                                    //loan schedule
                                    $loan = Loan::find($loan->id);
                                    $pdf = PDF::loadView('loan::loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
                                    $message->attachData($pdf->output(),
                                        trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf",
                                        ['mime' => 'application/pdf']);
                                }
                            });
                            //log sms
                            log_campaign([
                                'client_id' => $loan->client_id,
                                'communication_campaign_id' => $key->id,
                                'campaign_type' => $key->campaign_type,
                                'description' => $description,
                                'send_to' => $loan->email,
                                'status' => 'sent',
                                'campaign_name' => $key->name
                            ]);
                        }
                    }
                }
            }
            //check if we should move the schedule
            if (!empty($key->schedule_frequency) & !empty($key->schedule_frequency_type)) {
                $key->scheduled_date = Carbon::now()->add($key->schedule_frequency, $key->schedule_frequency_type)->format("Y-m-d");
                $key->scheduled_next_run_date = Carbon::now()->add($key->schedule_frequency, $key->schedule_frequency_type)->format("Y-m-d");
                $key->scheduled_last_run_date = Carbon::now()->format("Y-m-d");
                $key->save();
            }

        }
        $this->info("Schedule ran successfully");

    }


}
