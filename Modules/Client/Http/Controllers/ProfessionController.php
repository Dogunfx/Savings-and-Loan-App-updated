<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientType;
use Modules\Client\Entities\Profession;
use Yajra\DataTables\Facades\DataTables;

class ProfessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.professions.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.professions.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.professions.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.professions.destroy'])->only(['destroy']);

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
        $data = Profession::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage);
        return theme_view('client::profession.index',compact('data'));
    }

    public function get_professions(Request $request)
    {
        $query = Profession::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('client.clients.professions.edit')) {
                $action .= '<li><a href="' . url('client/profession/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('client.clients.professions.destroy')) {
                $action .= '<li><a href="' . url('client/profession/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('client/profession/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('client::profession.create');
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
        $profession = new Profession();
        $profession->name = $request->name;
        $profession->save();
        activity()->on($profession)
            ->withProperties(['id' => $profession->id])
            ->log('Create Profession');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/profession');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $profession = Profession::find($id);
        return theme_view('client::profession.show', compact('profession'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $profession = Profession::find($id);
        return theme_view('client::profession.edit', compact('profession'));
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
        $profession = Profession::find($id);
        $profession->name = $request->name;
        $profession->save();
        activity()->on($profession)
            ->withProperties(['id' => $profession->id])
            ->log('Update Profession');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/profession');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $profession = Profession::find($id);
        $profession->delete();
        activity()->on($profession)
            ->withProperties(['id' => $profession->id])
            ->log('Delete Profession');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
