<?php

namespace Modules\Income\Http\Controllers;

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
use Modules\Income\Entities\Income;
use Modules\Income\Entities\IncomeType;
use Yajra\DataTables\Facades\DataTables;

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:income.income.index'])->only(['index', 'show', 'get_income']);
        $this->middleware(['permission:income.income.create'])->only(['create', 'store']);
        $this->middleware(['permission:income.income.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:income.income.destroy'])->only(['destroy']);

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
        $data = Income::leftJoin('income_types', 'income_types.id', 'income.income_type_id')
            ->leftJoin('chart_of_accounts as income_chart', 'income_chart.id', 'income.income_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'income.asset_chart_of_account_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('id', 'like', "%$search%");
            })
            ->selectRaw('income.*,income_types.name income_type,income_chart.name income_chart_of_account,assets_chart.name asset_chart_of_account')
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('income::income.index', compact('data'));
    }

    public function get_income(Request $request)
    {
        $query = Income::leftJoin('income_types', 'income_types.id', 'income.income_type_id')
            ->leftJoin('chart_of_accounts as income_chart', 'income_chart.id', 'income.income_chart_of_account_id')
            ->leftJoin('chart_of_accounts as assets_chart', 'assets_chart.id', 'income.asset_chart_of_account_id')
            ->selectRaw('income.*,income_types.name income_type,income_chart.name income_chart_of_account,assets_chart.name asset_chart_of_account');
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '';
            if (Auth::user()->hasPermissionTo('income.income.edit')) {
                $action .= '<a href="' . url('income/' . $data->id . '/edit') . '" class="m-2"><i class="fa fa-edit"></i></a>';
            }
            if (Auth::user()->hasPermissionTo('income.income.destroy')) {
                $action .= '<a href="' . url('income/' . $data->id . '/destroy') . '" class="m-2 confirm"><i class="fa fa-trash"></i></a>';
            }
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('income/' . $data->id . '/show') . '">' . $data->id . '</a>';

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
        $income_types = IncomeType::all();
        $currencies = Currency::all();
        $branches = Branch::all();
        return theme_view('income::income.create', compact('assets', 'incomes', 'income_types', 'currencies', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'income_type_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'currency_id' => ['required'],
            'branch_id' => ['required'],
        ]);
        $income = new Income();
        $income->created_by_id = Auth::id();
        $income->income_type_id = $request->income_type_id;
        $income->currency_id = $request->currency_id;
        $income->branch_id = $request->branch_id;
        $income->income_chart_of_account_id = $request->income_chart_of_account_id;
        $income->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $income->amount = $request->amount;
        $income->date = $request->date;
        $income->description = $request->description;
        $income->save();
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'income';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //store journal entries
        if (!empty($request->income_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $income->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->income_chart_of_account_id;
            $journal_entry->transaction_type = 'income';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $request->amount;
            $journal_entry->reference = $income->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        if (!empty($request->asset_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $income->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->asset_chart_of_account_id;
            $journal_entry->transaction_type = 'income';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $request->amount;
            $journal_entry->reference = $income->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        activity()->on($income)
            ->withProperties(['id' => $income->id])
            ->log('Create Income');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('income');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $income = Income::find($id);
        return theme_view('income::income.show', compact('income'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $income = Income::find($id);
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $incomes = ChartOfAccount::where('account_type', 'income')->get();
        $income_types = IncomeType::all();
        $currencies = Currency::all();
        $branches = Branch::all();
        return theme_view('income::income.edit', compact('income', 'assets', 'incomes', 'income_types', 'currencies', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $income = Income::find($id);
        $request->validate([
            'income_type_id' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'currency_id' => ['required'],
            'branch_id' => ['required'],
        ]);

        $income->income_type_id = $request->income_type_id;
        $income->income_chart_of_account_id = $request->income_chart_of_account_id;
        $income->branch_id = $request->branch_id;
        $income->currency_id = $request->currency_id;
        $income->asset_chart_of_account_id = $request->asset_chart_of_account_id;
        $income->amount = $request->amount;
        $income->date = $request->date;

        $income->description = $request->description;
        $income->save();
        JournalEntry::where('transaction_number', $income->id)->where('transaction_type', 'income')->delete();
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'income';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //store journal entries
        if (!empty($request->income_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $income->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->income_chart_of_account_id;
            $journal_entry->transaction_type = 'income';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $request->amount;
            $journal_entry->reference = $income->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        if (!empty($request->asset_chart_of_account_id)) {
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = $income->id;
            $journal_entry->branch_id = $request->branch_id;
            $journal_entry->currency_id = $request->currency_id;
            $journal_entry->chart_of_account_id = $request->asset_chart_of_account_id;
            $journal_entry->transaction_type = 'income';
            $journal_entry->date = $request->date;
            $date = explode('-', $request->date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $request->amount;
            $journal_entry->reference = $income->id;
            $journal_entry->notes = $request->notes;
            $journal_entry->save();
        }
        activity()->on($income)
            ->withProperties(['id' => $income->id])
            ->log('Update Income');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('income');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $income = Income::find($id);
        $income->delete();
        JournalEntry::where('transaction_number', $income->id)->where('transaction_type', 'income')->delete();
        activity()->on($income)
            ->withProperties(['id' => $income->id])
            ->log('Delete Income');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('income');
    }
}
