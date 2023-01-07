<?php

namespace Modules\Communication\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Modules\Communication\Entities\SmsGateway;
use Yajra\DataTables\Facades\DataTables;

class SmsGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','2fa']);
        $this->middleware(['permission:communication.sms_gateways.index'])->only(['index', 'show']);
        $this->middleware(['permission:communication.sms_gateways.create'])->only(['create', 'store']);
        $this->middleware(['permission:communication.sms_gateways.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:communication.sms_gateways.destroy'])->only(['destroy']);

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
        $data = SmsGateway::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('communication::sms_gateway.index',compact('data'));
    }

    public function get_sms_gateways(Request $request)
    {


        $query = SmsGateway::query();
        return DataTables::of($query)->editColumn('description', function ($data) {
            return '<span data-toggle="tooltip" title="' . $data->description . '">' . Str::words($data->description, 10);
        })->editColumn('active', function ($data) {
            if ($data->active == '0') {
                return '<span class="label label-warning">' . trans_choice('core::general.no', 1) . '</span>';
            }

            if ($data->active == '1') {
                return '<span class="label label-success">' . trans_choice('core::general.yes', 1) . '</span>';
            }

        })->editColumn('id', function ($data) {

            $action = '<a href="' . url('communication/sms_gateway/' . $data->id . '/show') . '" class="btn btn-info">' . $data->id . '</a>';

            return $action;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('communication.campaigns.edit')) {
                // $action .= '<li><a href="' . url('communication/campaign/' . $data->id . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('communication.sms_gateways.edit') && $data->trigger_type != 'direct') {
                $action .= '<li><a href="' . url('communication/sms_gateway/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('communication.sms_gateways.destroy')) {
                $action .= '<li><a href="' . url('communication/sms_gateway/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;

        })->rawColumns(['id', 'description', 'action', 'active'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('communication::sms_gateway.create');
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
            'to_name' => ['required'],
            'msg_name' => ['required'],
            'url' => ['required'],
            'active' => ['required'],
        ]);
        $sms_gateway = new SmsGateway();
        $sms_gateway->created_by_id = Auth::id();
        $sms_gateway->name = $request->name;
        $sms_gateway->to_name = $request->to_name;
        $sms_gateway->msg_name = $request->msg_name;
        $sms_gateway->url = $request->url;
        $sms_gateway->notes = $request->notes;
        $sms_gateway->active = $request->active;
        $sms_gateway->save();
        activity()->on($sms_gateway)
            ->withProperties(['id' => $sms_gateway->id])
            ->log('Create SMS Gateway');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('communication/sms_gateway');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $sms_gateway = SmsGateway::find($id);
        return theme_view('communication::sms_gateway.show', compact('sms_gateway'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $sms_gateway = SmsGateway::find($id);
        return theme_view('communication::sms_gateway.edit', compact('sms_gateway'));
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
            'to_name' => ['required'],
            'msg_name' => ['required'],
            'url' => ['required'],
            'active' => ['required'],
        ]);
        $sms_gateway = SmsGateway::find($id);
        $sms_gateway->name = $request->name;
        $sms_gateway->to_name = $request->to_name;
        $sms_gateway->msg_name = $request->msg_name;
        $sms_gateway->url = $request->url;
        $sms_gateway->notes = $request->notes;
        $sms_gateway->active = $request->active;
        $sms_gateway->save();
        activity()->on($sms_gateway)
            ->withProperties(['id' => $sms_gateway->id])
            ->log('Update SMS Gateway');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('communication/sms_gateway');
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $sms_gateway = SmsGateway::find($id);
        $sms_gateway->delete();
        activity()->on($sms_gateway)
            ->withProperties(['id' => $sms_gateway->id])
            ->log('Delete SMS Gateway');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
