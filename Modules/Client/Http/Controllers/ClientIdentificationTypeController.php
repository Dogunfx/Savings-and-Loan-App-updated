<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientIdentificationType;
use Modules\Client\Entities\ClientType;
use Yajra\DataTables\Facades\DataTables;

class ClientIdentificationTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.identification_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.identification_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.identification_types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.identification_types.destroy'])->only(['destroy']);

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
        $data = ClientIdentificationType::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('client::client_identification_type.index', compact('data'));
    }

    public function get_client_identification_types(Request $request)
    {
        $query = ClientIdentificationType::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('client.clients.identification_types.edit')) {
                $action .= '<li><a href="' . url('client/client_identification_type/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('client.clients.identification_types.destroy')) {
                $action .= '<li><a href="' . url('client/client_identification_type/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('client/client_identification_type/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('client::client_identification_type.create');
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
        ]);
        $client_identification_type = new ClientIdentificationType();
        $client_identification_type->name = $request->name;
        $client_identification_type->save();
        activity()->on($client_identification_type)
            ->withProperties(['id' => $client_identification_type->id])
            ->log('Create Client Identification Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/client_identification_type');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_identification_type = ClientIdentificationType::find($id);
        return theme_view('client::client_identification_type.show', compact('client_identification_type'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_identification_type = ClientIdentificationType::find($id);
        return theme_view('client::client_identification_type.edit', compact('client_identification_type'));
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
        ]);
        $client_identification_type = ClientIdentificationType::find($id);
        $client_identification_type->name = $request->name;
        $client_identification_type->save();
        activity()->on($client_identification_type)
            ->withProperties(['id' => $client_identification_type->id])
            ->log('Update Client Identification Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/client_identification_type');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client_identification_type = ClientIdentificationType::find($id);
        $client_identification_type->delete();
        activity()->on($client_identification_type)
            ->withProperties(['id' => $client_identification_type->id])
            ->log('Delete Client Identification Type');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
