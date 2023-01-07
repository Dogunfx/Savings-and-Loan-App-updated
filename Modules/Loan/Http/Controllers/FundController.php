<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Profession;
use Modules\Loan\Entities\Fund;
use Yajra\DataTables\Facades\DataTables;

class FundController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.funds.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.funds.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.funds.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.funds.destroy'])->only(['destroy']);

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
        $data = Fund::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::fund.index',compact('data'));
    }

    public function get_funds(Request $request)
    {
        $query = Fund::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('loan.loans.funds.edit')) {
                $action .= '<li><a href="' . url('loan/fund/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('loan.loans.funds.destroy')) {
                $action .= '<li><a href="' . url('loan/fund/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/fund/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('loan::fund.create');
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
        $fund = new Fund();
        $fund->name = $request->name;
        $fund->save();
        activity()->on($fund)
            ->withProperties(['id' => $fund->id])
            ->log('Create Fund');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/fund');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $fund = Fund::find($id);
        return theme_view('loan::fund.show', compact('fund'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $fund = Fund::find($id);
        return theme_view('loan::fund.edit', compact('fund'));
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
        $fund = Fund::find($id);
        $fund->name = $request->name;
        $fund->save();
        activity()->on($fund)
            ->withProperties(['id' => $fund->id])
            ->log('Update Fund');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/fund');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $fund = Fund::find($id);
        $fund->delete();
        activity()->on($fund)
            ->withProperties(['id' => $fund->id])
            ->log('Delete Fund');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
