<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Expense\Entities\ExpenseType;
use Modules\Loan\Entities\Fund;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:expense.expenses.types.index'])->only(['index', 'show', 'get_expense_types']);
        $this->middleware(['permission:expense.expenses.types.create'])->only(['create', 'store']);
        $this->middleware(['permission:expense.expenses.types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:expense.expenses.types.destroy'])->only(['destroy']);

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
        $data = ExpenseType::leftJoin('chart_of_accounts as expenses_chart', 'expenses_chart.id', 'expense_types.expense_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'expense_types.asset_chart_of_account_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->selectRaw('expense_types.*,expenses_chart.name expense_chart_of_account,assets_chart.name asset_chart_of_account')
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('expense::type.index', compact('data'));
    }

    public function get_expense_types(Request $request)
    {
        $query = ExpenseType::leftJoin('chart_of_accounts as expenses_chart', 'expenses_chart.id', 'expense_types.expense_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'expense_types.asset_chart_of_account_id')
            ->selectRaw('expense_types.*,expenses_chart.name expense_chart_of_account,assets_chart.name asset_chart_of_account');
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '';
            if (Auth::user()->hasPermissionTo('expense.expenses.types.edit')) {
                $action .= '<a href="' . url('expense/type/' . $data->id . '/edit') . '" class="m-2"><i class="fa fa-edit"></i></a>';
            }
            if (Auth::user()->hasPermissionTo('expense.expenses.types.destroy')) {
                $action .= '<a href="' . url('expense/type/' . $data->id . '/destroy') . '" class="m-2 confirm"><i class="fa fa-trash"></i></a>';
            }
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('expense/type/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        return theme_view('expense::type.create', compact('expenses', 'assets'));
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
        $expense_type = new ExpenseType();
        $expense_type->name = $request->name;
        $expense_type->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expense_type->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expense_type->save();
        activity()->on($expense_type)
            ->withProperties(['id' => $expense_type->id])
            ->log('Create Expense Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('expense/type');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $expense_type = ExpenseType::find($id);
        return theme_view('expense::type.show', compact('expense_type'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $expense_type = ExpenseType::find($id);
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        return theme_view('expense::type.edit', compact('expense_type', 'assets', 'expenses'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $expense_type = ExpenseType::find($id);
        $request->validate([
            'name' => ['required'],
        ]);
        $expense_type->name = $request->name;
        $expense_type->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expense_type->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expense_type->save();
        activity()->on($expense_type)
            ->withProperties(['id' => $expense_type->id])
            ->log('Update Expense Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('expense/type');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $expense_type = ExpenseType::find($id);
        $expense_type->delete();
        activity()->on($expense_type)
            ->withProperties(['id' => $expense_type->id])
            ->log('Delete Expense Type');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('expense/type');
    }
}
