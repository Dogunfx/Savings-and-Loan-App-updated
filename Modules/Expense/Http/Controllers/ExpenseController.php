<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Branch\Entities\Branch;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\PaymentDetail;
use Modules\Expense\Entities\Expense;
use Modules\Expense\Entities\ExpenseType;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:expense.expenses.index'])->only(['index', 'show', 'get_expenses']);
        $this->middleware(['permission:expense.expenses.create'])->only(['create', 'store']);
        $this->middleware(['permission:expense.expenses.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:expense.expenses.destroy'])->only(['destroy']);

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
        $data = Expense::leftJoin('expense_types', 'expense_types.id', 'expenses.expense_type_id')
            ->leftJoin('chart_of_accounts as expenses_chart', 'expenses_chart.id', 'expenses.expense_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'expenses.asset_chart_of_account_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('id', 'like', "%$search%");
            })
            ->selectRaw('expenses.*,expense_types.name expense_type,expenses_chart.name expense_chart_of_account,assets_chart.name asset_chart_of_account')
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('expense::expense.index', compact('data'));
    }

    public function get_expenses(Request $request)
    {
        $query = Expense::leftJoin('expense_types', 'expense_types.id', 'expenses.expense_type_id')
            ->leftJoin('chart_of_accounts as expenses_chart', 'expenses_chart.id', 'expenses.expense_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'expenses.asset_chart_of_account_id')
            ->selectRaw('expenses.*,expense_types.name expense_type,expenses_chart.name expense_chart_of_account,assets_chart.name asset_chart_of_account');
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '';
            if (Auth::user()->hasPermissionTo('expense.expenses.edit')) {
                $action .= '<a href="' . url('expense/' . $data->id . '/edit') . '" class="m-2"><i class="fa fa-edit"></i></a>';
            }
            if (Auth::user()->hasPermissionTo('expense.expenses.destroy')) {
                $action .= '<a href="' . url('expense/' . $data->id . '/destroy') . '" class="m-2 confirm"><i class="fa fa-trash"></i></a>';
            }
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('expense/' . $data->id . '/show') . '">' . $data->id . '</a>';

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
        $expense_types = ExpenseType::all();
        $currencies = Currency::all();
        $branches = Branch::all();
        return theme_view('expense::expense.create', compact('assets', 'expenses', 'expense_types', 'currencies', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'currency_id' => ['required'],
            'branch_id' => ['required'],
        ]);
        $expense = new Expense();
        $expense->created_by_id = Auth::id();
        $expense->expense_type_id = $request->expense_type_id;
        $expense->currency_id = $request->currency_id;
        $expense->branch_id = $request->branch_id;
        $expense->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expense->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expense->amount = $request->amount;
        $expense->date = $request->date;
        $expense->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            $expense->recur_end_date = $request->recur_end_date;
            $expense->recur_type = $request->recur_type;
        }
        $expense->description = $request->description;
        $expense->save();
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'expense';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //store journal entries
        if (!empty($request->expense_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->expense_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $request->amount;
            $journal_entry->reference = $expense->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        if (!empty($request->asset_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->asset_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $request->amount;
            $journal_entry->reference = $expense->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        activity()->on($expense)
            ->withProperties(['id' => $expense->id])
            ->log('Create Expense');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('expense');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $expense = Expense::find($id);
        return theme_view('expense::expense.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $expense = Expense::find($id);
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        $expense_types = ExpenseType::all();
        $currencies = Currency::all();
        $branches = Branch::all();
        return theme_view('expense::expense.edit', compact('expense', 'assets', 'expenses', 'expense_types', 'currencies', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);
        $request->validate([
            'expense_type_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'currency_id' => ['required'],
            'branch_id' => ['required'],
        ]);

        $expense->expense_type_id = $request->expense_type_id;
        $expense->expense_chart_of_account_id = $request->expense_chart_of_account_id;
        $expense->branch_id = $request->branch_id;
        $expense->currency_id = $request->currency_id;
        $expense->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $expense->amount = $request->amount;
        $expense->date = $request->date;
        $expense->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            $expense->recur_end_date = $request->recur_end_date;
            $expense->recur_type = $request->recur_type;
        }
        $expense->description = $request->description;
        $expense->save();
        JournalEntry::where('transaction_number', $expense->id)->where('transaction_type', 'expense')->delete();
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'expense';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //store journal entries
        if (!empty($request->expense_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->expense_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $request->amount;
            $journal_entry->reference = $expense->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        if (!empty($request->asset_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $expense->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->asset_chart_of_account_id;
            $journal_entry->transaction_type = 'expense';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $request->amount;
            $journal_entry->reference = $expense->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        activity()->on($expense)
            ->withProperties(['id' => $expense->id])
            ->log('Update Expense');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('expense');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);
        $expense->delete();
        JournalEntry::where('transaction_number', $expense->id)->where('transaction_type', 'expense')->delete();
        activity()->on($expense)
            ->withProperties(['id' => $expense->id])
            ->log('Delete Expense');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('expense');
    }
}
