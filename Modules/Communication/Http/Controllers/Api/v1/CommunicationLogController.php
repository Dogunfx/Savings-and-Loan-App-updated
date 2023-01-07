<?php

namespace Modules\Communication\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Communication\Entities\CommunicationCampaignLog;
use Modules\Communication\Entities\SmsGateway;
use Yajra\DataTables\Facades\DataTables;

class CommunicationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
        $limit = $request->limit ? $request->limit : 20;
        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $campaign_type = $request->campaign_type;
        $data = DB::table("communication_campaign_logs")->selectRaw("communication_campaign_logs.description,communication_campaign_logs.send_to,communication_campaign_logs.campaign_name,communication_campaign_logs.campaign_type,communication_campaign_logs.id,communication_campaign_logs.status,communication_campaign_logs.created_at")->when($status, function ($query) use ($status) {
            $query->where("communication_campaign_logs.status", $status);
        })->when($campaign_type, function ($query) use ($campaign_type) {
            $query->where("communication_campaign_logs.campaign_type", $campaign_type);
        })->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween("communication_campaign_logs.created_at", [$start_date, $end_date]);
        })->paginate($limit);
        return response()->json([$data]);
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $communication_campaign_log = CommunicationCampaignLog::find($id);
        return response()->json(['data' => $communication_campaign_log]);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        CommunicationCampaignLog::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
