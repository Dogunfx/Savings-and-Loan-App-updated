<?php

namespace Modules\Communication\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Communication\Entities\CommunicationCampaign;
use Modules\Communication\Entities\CommunicationCampaignAttachmentType;
use Modules\Communication\Entities\CommunicationCampaignBusinessRule;
use Modules\Communication\Entities\SmsGateway;
use Modules\Communication\Jobs\ProcessDirectCampaigns;
use Modules\Loan\Entities\LoanProduct;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;

class CommunicationCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:communication.campaigns.index'])->only(['index', 'show']);
        $this->middleware(['permission:communication.campaigns.create'])->only(['create', 'store']);
        $this->middleware(['permission:communication.campaigns.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:communication.campaigns.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $status = $request->status;
        $branch_id = $request->branch_id;
        $trigger_type = $request->trigger_type;
        $campaign_type = $request->campaign_type;
        $data = DB::table("communication_campaigns")
            ->leftJoin('users', 'users.id', 'communication_campaigns.created_by_id')
            ->leftJoin('branches', 'branches.id', 'communication_campaigns.branch_id')
            ->selectRaw("communication_campaigns.id,communication_campaigns.name,communication_campaigns.campaign_type,communication_campaigns.trigger_type,communication_campaigns.status,communication_campaigns.description,communication_campaigns.created_at,concat(users.first_name,' ',users.last_name) created_by,branches.name branch")->when($status, function ($query) use ($status) {
                $query->where("communication_campaigns.status", $status);
            })->when($campaign_type, function ($query) use ($campaign_type) {
                $query->where("communication_campaigns.campaign_type", $campaign_type);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("communication_campaigns.branch_id", $branch_id);
            })->when($trigger_type, function ($query) use ($trigger_type) {
                $query->where("communication_campaigns.trigger_type", $trigger_type);
            })->paginate($limit);
        return response()->json([$data]);
    }

    public function get_business_rules($id)
    {
        $data = CommunicationCampaignBusinessRule::get();
        return response()->json(['data' => $data]);
    }

    public function get_attachments_types($id)
    {
        $data = CommunicationCampaignAttachmentType::get();
        return response()->json(['data' => $data]);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'sms_gateway_id' => ['required_if:campaign_type,sms'],
            'scheduled_date' => ['required_if:trigger_type,schedule'],
            'scheduled_time' => ['required_if:trigger_type,schedule'],
            'from_x' => ['required_if:communication_campaign_business_rule_id,4,5,6,8,9,10,12,13,14'],
            'to_y' => ['required_if:communication_campaign_business_rule_id,4,5,6,8,9,10,12,13,14'],
            'cycle_x' => ['required_if:communication_campaign_business_rule_id,3,5'],
            'cycle_y' => ['required_if:communication_campaign_business_rule_id,3,5'],
            'overdue_x' => ['required_if:communication_campaign_business_rule_id,8,10'],
            'overdue_y' => ['required_if:communication_campaign_business_rule_id,8,10'],
            'description' => ['required'],
            'status' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $communication_campaign = new CommunicationCampaign();
            $communication_campaign->created_by_id = Auth::id();
            $communication_campaign->subject = $request->subject;
            $communication_campaign->name = $request->name;
            $communication_campaign->sms_gateway_id = $request->sms_gateway_id;
            $communication_campaign->communication_campaign_business_rule_id = $request->communication_campaign_business_rule_id;
            $communication_campaign->communication_campaign_attachment_type_id = $request->communication_campaign_attachment_type_id;
            $communication_campaign->branch_id = $request->branch_id;
            $communication_campaign->loan_officer_id = $request->loan_officer_id;
            $communication_campaign->loan_product_id = $request->loan_product_id;
            $communication_campaign->campaign_type = $request->campaign_type;
            $communication_campaign->trigger_type = $request->trigger_type;
            if ($request->trigger_type == 'schedule') {
                $communication_campaign->scheduled_date = $request->scheduled_date;
                $communication_campaign->scheduled_time = $request->scheduled_time;
                $communication_campaign->schedule_frequency = $request->schedule_frequency;
                $communication_campaign->schedule_frequency_type = $request->schedule_frequency_type;
            }
            $communication_campaign->from_x = $request->from_x;
            $communication_campaign->to_y = $request->to_y;
            $communication_campaign->cycle_x = $request->cycle_x;
            $communication_campaign->cycle_y = $request->cycle_y;
            $communication_campaign->overdue_x = $request->overdue_x;
            $communication_campaign->overdue_y = $request->overdue_y;
            $communication_campaign->status = $request->status;
            $communication_campaign->description = $request->description;
            $communication_campaign->save();
            if ($request->trigger_type == 'direct') {
                ProcessDirectCampaigns::dispatch($communication_campaign);
            }
            return response()->json(['data' => $communication_campaign, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $communication_campaign = CommunicationCampaign::find($id);
        return response()->json(['data' => $communication_campaign]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $communication_campaign = CommunicationCampaign::find($id);
        return response()->json(['data' => $communication_campaign]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'sms_gateway_id' => ['required_if:campaign_type,sms'],
            'scheduled_date' => ['required_if:trigger_type,schedule'],
            'scheduled_time' => ['required_if:trigger_type,schedule'],
            'from_x' => ['required_if:communication_campaign_business_rule_id,4,5,6,8,9,10,12,13,14'],
            'to_y' => ['required_if:communication_campaign_business_rule_id,4,5,6,8,9,10,12,13,14'],
            'cycle_x' => ['required_if:communication_campaign_business_rule_id,3,5'],
            'cycle_y' => ['required_if:communication_campaign_business_rule_id,3,5'],
            'overdue_x' => ['required_if:communication_campaign_business_rule_id,8,10'],
            'overdue_y' => ['required_if:communication_campaign_business_rule_id,8,10'],
            'description' => ['required'],
            'status' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $communication_campaign = CommunicationCampaign::find($id);
            $communication_campaign->name = $request->name;
            $communication_campaign->subject = $request->subject;
            $communication_campaign->sms_gateway_id = $request->sms_gateway_id;
            $communication_campaign->communication_campaign_business_rule_id = $request->communication_campaign_business_rule_id;
            $communication_campaign->communication_campaign_attachment_type_id = $request->communication_campaign_attachment_type_id;
            $communication_campaign->branch_id = $request->branch_id;
            $communication_campaign->loan_officer_id = $request->loan_officer_id;
            $communication_campaign->loan_product_id = $request->loan_product_id;
            $communication_campaign->campaign_type = $request->campaign_type;
            $communication_campaign->trigger_type = $request->trigger_type;
            if ($request->trigger_type == 'schedule') {
                $communication_campaign->scheduled_date = $request->scheduled_date;
                $communication_campaign->scheduled_time = $request->scheduled_time;
                $communication_campaign->schedule_frequency = $request->schedule_frequency;
                $communication_campaign->schedule_frequency_type = $request->schedule_frequency_type;
            }
            $communication_campaign->from_x = $request->from_x;
            $communication_campaign->to_y = $request->to_y;
            $communication_campaign->cycle_x = $request->cycle_x;
            $communication_campaign->cycle_y = $request->cycle_y;
            $communication_campaign->overdue_x = $request->overdue_x;
            $communication_campaign->overdue_y = $request->overdue_y;
            $communication_campaign->status = $request->status;
            $communication_campaign->description = $request->description;
            $communication_campaign->save();
            return response()->json(['data' => $communication_campaign, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        CommunicationCampaign::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
