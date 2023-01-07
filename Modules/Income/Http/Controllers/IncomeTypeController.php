<?php

namespace Modules\Income\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Income\Entities\IncomeType;
use Modules\Loan\Entities\Fund;
use Yajra\DataTables\Facades\DataTables;

class IncomeTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:income.income.types.index'])->only(['index', 'show', 'get_income_types']);
        $this->middleware(['permission:income.income.types.create'])->only(['create', 'store']);
        $this->middleware(['permission:income.income.types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:income.income.types.destroy'])->only(['destroy']);

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
        $data = IncomeType::leftJoin('chart_of_accounts as income_chart', 'income_chart.id', 'income_types.income_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'income_types.asset_chart_of_account_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->selectRaw('income_types.*,income_chart.name income_chart_of_account,assets_chart.name asset_chart_of_account')
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('income::type.index', compact('data'));
    }

    public function get_income_types(Request $request)
    {
        $query = IncomeType::leftJoin('chart_of_accounts as income_chart', 'income_chart.id', 'income_types.income_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'income_types.asset_chart_of_account_id')
            ->selectRaw('income_types.*,income_chart.name income_chart_of_account,assets_chart.name asset_chart_of_account');
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '';
            if (Auth::user()->hasPermissionTo('income.income.types.edit')) {
                $action .= '<a href="' . url('income/type/' . $data->id . '/edit') . '" class="m-2"><i class="fa fa-edit"></i></a>';
            }
            if (Auth::user()->hasPermissionTo('income.income.types.destroy')) {
                $action .= '<a href="' . url('income/type/' . $data->id . '/destroy') . '" class="m-2 confirm"><i class="fa fa-trash"></i></a>';
            }
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('income/type/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $incomes = ChartOfAccount::where('account_type', 'income')->get();
        return theme_view('income::type.create', compact('incomes', 'assets'));
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
        $income_type = new IncomeType();
        $income_type->name = $request->name;
        $income_type->income_chart_of_account_id = $request->income_chart_of_account_id;
        $income_type->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $income_type->save();
        activity()->on($income_type)
            ->withProperties(['id' => $income_type->id])
            ->log('Create Income Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('income/type');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $income_type = IncomeType::find($id);
        return theme_view('income::type.show', compact('income_type'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $income_type = IncomeType::find($id);
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $incomes = ChartOfAccount::where('account_type', 'income')->get();
        return theme_view('income::type.edit', compact('income_type', 'assets', 'incomes'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $income_type = IncomeType::find($id);
        $request->validate([
            'name' => ['required'],
        ]);
        $income_type->name = $request->name;
        $income_type->income_chart_of_account_id = $request->income_chart_of_account_id;
        $income_type->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $income_type->save();
        activity()->on($income_type)
            ->withProperties(['id' => $income_type->id])
            ->log('Update Income Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('income/type');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $income_type = IncomeType::find($id);
        $income_type->delete();
        activity()->on($income_type)
            ->withProperties(['id' => $income_type->id])
            ->log('Delete Income Type');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('income/type');
    }
}
