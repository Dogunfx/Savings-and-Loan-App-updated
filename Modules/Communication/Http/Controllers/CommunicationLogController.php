<?php

namespace Modules\Communication\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Modules\Communication\Entities\CommunicationCampaignLog;
use Modules\Communication\Entities\SmsGateway;
use Yajra\DataTables\Facades\DataTables;

class CommunicationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:communication.logs.index'])->only(['index', 'show', 'get_logs']);
        $this->middleware(['permission:communication.logs.create'])->only(['create', 'store']);
        $this->middleware(['permission:communication.logs.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:communication.logs.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $campaign_type = $request->campaign_type;
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = CommunicationCampaignLog::leftJoin('communication_campaigns', 'communication_campaigns.id', 'communication_campaign_logs.communication_campaign_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('communication_campaign_logs.id', 'like', "%$search%");
            })
            ->when($campaign_type, function ($query) use ($campaign_type) {
                $query->where("communication_campaign_logs.campaign_type", $campaign_type);
            })
            ->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween("communication_campaign_logs.created_at", [$start_date, $end_date]);
            })
            ->selectRaw("communication_campaign_logs.description,communication_campaign_logs.send_to,communication_campaign_logs.campaign_name,communication_campaign_logs.campaign_type,communication_campaign_logs.id,communication_campaign_logs.status,communication_campaign_logs.created_at,communication_campaigns.name campaign")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('communication::log.index', compact('data', 'status', 'campaign_type'));
    }

    public function get_logs(Request $request)
    {

        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $campaign_type = $request->campaign_type;

        $query = DB::table("communication_campaign_logs")
            ->selectRaw("communication_campaign_logs.description,communication_campaign_logs.send_to,communication_campaign_logs.campaign_name,communication_campaign_logs.campaign_type,communication_campaign_logs.id,communication_campaign_logs.status,communication_campaign_logs.created_at")
            ->when($status, function ($query) use ($status) {
                $query->where("communication_campaign_logs.status", $status);
            })->when($campaign_type, function ($query) use ($campaign_type) {
                $query->where("communication_campaign_logs.campaign_type", $campaign_type);
            })->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween("communication_campaign_logs.created_at", [$start_date, $end_date]);
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
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }

            if ($data->status == 'sent') {
                return '<span class="label label-warning">' . trans_choice('communication::general.sent', 1) . '</span>';
            }
            if ($data->status == 'delivered') {
                return '<span class="label label-info">' . trans_choice('communication::general.delivered', 1) . '</span>';
            }
            if ($data->status == 'failed') {
                return '<span class="label label-danger">' . trans_choice('communication::general.failed', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('communication/log/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('communicationlog//' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'description', 'action', 'status'])->make(true);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $communication_campaign_log = CommunicationCampaignLog::find($id);
        return theme_view('communication::log.show', compact('communication_campaign_log'));
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        CommunicationCampaignLog::destroy($id);
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
