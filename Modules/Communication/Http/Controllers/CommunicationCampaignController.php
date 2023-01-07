<?php

namespace Modules\Communication\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Communication\Entities\CommunicationCampaign;
use Modules\Communication\Entities\CommunicationCampaignAttachmentType;
use Modules\Communication\Entities\CommunicationCampaignBusinessRule;
use Modules\Communication\Entities\SmsGateway;
use Modules\Communication\Jobs\ProcessDirectCampaigns;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanProduct;
use Modules\Setting\Entities\Setting;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;

class CommunicationCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
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
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = CommunicationCampaign::leftJoin('users', 'users.id', 'communication_campaigns.created_by_id')
            ->leftJoin('branches', 'branches.id', 'communication_campaigns.branch_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('communication_campaigns.id', 'like', "%$search%");
                $query->orWhere('communication_campaigns.name', 'like', "%$search%");
            })
            ->selectRaw("communication_campaigns.id,communication_campaigns.name,communication_campaigns.campaign_type,communication_campaigns.trigger_type,communication_campaigns.status,communication_campaigns.description,communication_campaigns.created_at,concat(users.first_name,' ',users.last_name) created_by,branches.name branch")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('communication::campaign.index', compact('data'));
    }

    public function get_communication_campaigns(Request $request)
    {

        $status = $request->status;
        $branch_id = $request->branch_id;
        $trigger_type = $request->trigger_type;
        $campaign_type = $request->campaign_type;

        $query = DB::table("communication_campaigns")
            ->leftJoin('users', 'users.id', 'communication_campaigns.created_by_id')
            ->leftJoin('branches', 'branches.id', 'communication_campaigns.branch_id')
            ->selectRaw("communication_campaigns.id,communication_campaigns.name,communication_campaigns.campaign_type,communication_campaigns.trigger_type,communication_campaigns.status,communication_campaigns.description,communication_campaigns.created_at,concat(users.first_name,' ',users.last_name) created_by,branches.name branch")
            ->when($status, function ($query) use ($status) {
                $query->where("communication_campaigns.status", $status);
            })->when($campaign_type, function ($query) use ($campaign_type) {
                $query->where("communication_campaigns.campaign_type", $campaign_type);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("communication_campaigns.branch_id", $branch_id);
            })->when($trigger_type, function ($query) use ($trigger_type) {
                $query->where("communication_campaigns.trigger_type", $trigger_type);
            });
        return DataTables::of($query)->editColumn('description', function ($data) {
            return '<span data-toggle="tooltip" title="' . $data->description . '">' . Str::words($data->description, 10);
        })->editColumn('campaign_type', function ($data) {
            if ($data->campaign_type == 'sms') {
                return trans_choice('communication::general.sms', 1);
            }
            if ($data->campaign_type == 'email') {
                return trans_choice('communication::general.email', 1);
            }
        })->editColumn('trigger_type', function ($data) {
            if ($data->trigger_type == 'direct') {
                return trans_choice('communication::general.direct', 1);
            }
            if ($data->trigger_type == 'schedule') {
                return trans_choice('communication::general.schedule', 1);
            }
            if ($data->trigger_type == 'triggered') {
                return trans_choice('communication::general.triggered', 1);
            }
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }

            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('communication::general.active', 1) . '</span>';
            }
            if ($data->status == 'inactive') {
                return '<span class="label label-warning">' . trans_choice('communication::general.inactive', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('communication::general.closed', 1) . '</span>';
            }
            if ($data->status == 'done') {
                return '<span class="label label-success">' . trans_choice('communication::general.done', 1) . '</span>';
            }

        })->editColumn('id', function ($data) {

            $action = '<a href="' . url('communication/campaign/' . $data->id . '/show') . '" class="btn btn-info">' . $data->id . '</a>';

            return $action;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('communication.campaigns.edit')) {
                // $action .= '<li><a href="' . url('communication/campaign/' . $data->id . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('communication.campaigns.edit') && $data->trigger_type != 'direct') {
                $action .= '<li><a href="' . url('communication/campaign/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('communication.campaigns.destroy')) {
                $action .= '<li><a href="' . url('communication/campaign/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;

        })->rawColumns(['id', 'description', 'action', 'status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_rules = [];
        foreach (CommunicationCampaignBusinessRule::get() as $key) {
            $business_rules[$key->id] = $key;
        }
        $attachments_types = [];
        foreach (CommunicationCampaignAttachmentType::get() as $key) {
            $attachments_types[$key->id] = $key;
        }
        $loan_products = [];
        foreach (LoanProduct::get() as $key) {
            $loan_products[$key->id] = $key;
        }
        $branches = [];
        foreach (Branch::get() as $key) {
            $branches[$key->id] = $key;
        }
        $users = [];
        foreach (User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get() as $key) {
            $users[$key->id] = $key;
        }
        \JavaScript::put([
            'business_rules' => $business_rules,
            'attachment_types' => $attachments_types,
            'loan_products' => $loan_products,
            'branches' => $branches,
            'users' => $users,
            'sms_gateways' => SmsGateway::where('active', 1)->get()
        ]);
        return theme_view('communication::campaign.create', compact('business_rules', 'attachments_types', 'loan_products', 'branches', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
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
        activity()->on($communication_campaign)
            ->withProperties(['id' => $communication_campaign->id])
            ->log('Create Communication Campaign');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('communication/campaign');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $communication_campaign = CommunicationCampaign::find($id);
        return theme_view('communication::campaign.show', compact('communication_campaign'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $communication_campaign = CommunicationCampaign::find($id);
        if ($communication_campaign->trigger_type == 'direct') {
            redirect()->back();
        }
        $business_rules = [];
        foreach (CommunicationCampaignBusinessRule::get() as $key) {
            $business_rules[$key->id] = $key;
        }
        $attachments_types = [];
        foreach (CommunicationCampaignAttachmentType::get() as $key) {
            $attachments_types[$key->id] = $key;
        }
        $loan_products = [];
        foreach (LoanProduct::get() as $key) {
            $loan_products[$key->id] = $key;
        }
        $branches = [];
        foreach (Branch::get() as $key) {
            $branches[$key->id] = $key;
        }
        $users = [];
        foreach (User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get() as $key) {
            $users[$key->id] = $key;
        }
        \JavaScript::put([
            'business_rules' => $business_rules,
            'attachment_types' => $attachments_types,
            'loan_products' => $loan_products,
            'branches' => $branches,
            'users' => $users,
            'sms_gateways' => SmsGateway::where('active', 1)->get()
        ]);

        return theme_view('communication::campaign.edit', compact('communication_campaign', 'business_rules', 'attachments_types', 'loan_products', 'branches', 'users'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
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
        activity()->on($communication_campaign)
            ->withProperties(['id' => $communication_campaign->id])
            ->log('Update Communication Campaign');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('communication/campaign');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $communication_campaign = CommunicationCampaign::find($id);
        $communication_campaign->delete();
        activity()->on($communication_campaign)
            ->withProperties(['id' => $communication_campaign->id])
            ->log('Delete Communication Campaign');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
