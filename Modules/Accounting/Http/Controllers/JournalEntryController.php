<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Branch\Entities\Branch;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Yajra\DataTables\Facades\DataTables;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:accounting.journal_entries.index'])->only(['index', 'show', 'get_journal_entries']);
        $this->middleware(['permission:accounting.journal_entries.create'])->only(['create', 'store']);
        $this->middleware(['permission:accounting.journal_entries.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:accounting.journal_entries.reverse'])->only(['destroy', 'reverse']);

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
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $chart_of_account_id = $request->chart_of_account_id;
        $data = JournalEntry::leftJoin("branches", "branches.id", "journal_entries.branch_id")
            ->leftJoin("chart_of_accounts", "chart_of_accounts.id", "journal_entries.chart_of_account_id")
            ->leftJoin("users", "users.id", "journal_entries.created_by_id")
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween("journal_entries.date", [$start_date, $end_date]);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("journal_entries.branch_id", $branch_id);
            })
            ->when($chart_of_account_id, function ($query) use ($chart_of_account_id) {
                $query->where("journal_entries.chart_of_account_id", $chart_of_account_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('journal_entries.name', 'like', "%$search%");
                $query->orWhere('journal_entries.id', 'like', "%$search%");
                $query->orWhere('journal_entries.transaction_number', 'like', "%$search%");
                $query->orWhere('branches.name', 'like', "%$search%");
                $query->orWhere('users.first_name', 'like', "%$search%");
                $query->orWhere('users.last_name', 'like', "%$search%");
            })
            ->selectRaw("journal_entries.id,journal_entries.date,journal_entries.debit,journal_entries.credit,journal_entries.transaction_number,branches.name branch,chart_of_accounts.account_type,chart_of_accounts.name account_name,concat(users.first_name,' ',users.last_name) created_by")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('accounting::journal_entry.index', compact('data'));
    }

    public function get_journal_entries(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $chart_of_account_id = $request->chart_of_account_id;

        $query = DB::table("journal_entries")->leftJoin("branches", "branches.id", "journal_entries.branch_id")
            ->leftJoin("chart_of_accounts", "chart_of_accounts.id", "journal_entries.chart_of_account_id")
            ->leftJoin("users", "users.id", "journal_entries.created_by_id")
            ->when($end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween("journal_entries.date", [$start_date, $end_date]);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("journal_entries.branch_id", $branch_id);
            })
            ->when($chart_of_account_id, function ($query) use ($chart_of_account_id) {
                $query->where("journal_entries.chart_of_account_id", $chart_of_account_id);
            })
            ->selectRaw("journal_entries.id,journal_entries.date,journal_entries.debit,journal_entries.credit,journal_entries.transaction_number,branches.name branch,chart_of_accounts.account_type,chart_of_accounts.name account_name,concat(users.first_name,' ',users.last_name) created_by");
        return DataTables::of($query)->editColumn('debit', function ($data) {
            if (!empty($data->debit)) {
                return number_format($data->debit, 2);
            }
        })->editColumn('credit', function ($data) {
            if (!empty($data->credit)) {
                return number_format($data->credit, 2);
            }
        })->editColumn('account_type', function ($data) {
            if ($data->account_type == 'asset') {
                return trans_choice('accounting::general.asset', 1);
            }
            if ($data->account_type == 'expense') {
                return trans_choice('accounting::general.expense', 1);
            }
            if ($data->account_type == 'equity') {
                return trans_choice('accounting::general.equity', 1);
            }
            if ($data->account_type == 'liability') {
                return trans_choice('accounting::general.liability', 1);
            }
            if ($data->account_type == 'income') {
                return trans_choice('accounting::general.income', 1);
            }
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('accounting/journal_entry/' . $data->transaction_number . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('accounting/journal_entry/' . $data->transaction_number . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $chart_of_accounts = ChartOfAccount::where('active', 1)->orderBy('gl_code')->get();
        $currencies = Currency::all();
        $payment_types = PaymentType::where('active', 1)->get();
        $branches = Branch::all();
        return theme_view('accounting::journal_entry.create', compact('chart_of_accounts', 'currencies', 'payment_types', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => ['required'],
            'currency_id' => ['required'],
            'amount' => ['required'],
            'debit' => ['required'],
            'credit' => ['required'],
            'date' => ['required', 'date']
        ]);
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'journal_manual_entry';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        //debit account
        $transaction_number = uniqid();
        $journal_entry = new JournalEntry();
        $journal_entry->created_by_id = Auth::id();
        $journal_entry->payment_detail_id = $payment_detail->id;
        $journal_entry->transaction_number = $transaction_number;
        $journal_entry->branch_id = $request->branch_id;
        $journal_entry->currency_id = $request->currency_id;
        $journal_entry->chart_of_account_id = $request->debit;
        $journal_entry->transaction_type = 'manual_entry';
        $journal_entry->date = $request->date;
        $date = explode('-', $request->date);
        $journal_entry->month = $date[1];
        $journal_entry->year = $date[0];
        $journal_entry->debit = $request->amount;
        $journal_entry->reference = $request->reference;
        $journal_entry->manual_entry = 1;
        $journal_entry->notes = $request->notes;
        $journal_entry->save();
        //credit account
        $journal_entry = new JournalEntry();
        $journal_entry->created_by_id = Auth::id();
        $journal_entry->transaction_number = $transaction_number;
        $journal_entry->payment_detail_id = $payment_detail->id;
        $journal_entry->branch_id = $request->branch_id;
        $journal_entry->currency_id = $request->currency_id;
        $journal_entry->chart_of_account_id = $request->credit;
        $journal_entry->transaction_type = 'manual_entry';
        $journal_entry->date = $request->date;
        $date = explode('-', $request->date);
        $journal_entry->month = $date[1];
        $journal_entry->year = $date[0];
        $journal_entry->credit = $request->amount;
        $journal_entry->reference = $request->reference;
        $journal_entry->manual_entry = 1;
        $journal_entry->notes = $request->notes;
        $journal_entry->save();
        activity()->log('Create Journal Entry');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('accounting/journal_entry/' . $transaction_number . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $data = JournalEntry::where('transaction_number', $id)->with('chart_of_account')->with('branch')->get();
        return theme_view('accounting::journal_entry.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('accounting::journal_entry.edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {

    }

    public function reverse($id)
    {
        JournalEntry::where('transaction_number', $id)->update(['reversed' => 1, 'reversible' => 0]);
        //create new transactions to reverse these
        $transaction_number = uniqid();

        foreach (JournalEntry::where('transaction_number', $id)->get() as $key) {
            if (empty($key->debit)) {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = $transaction_number;
                $journal_entry->payment_detail_id = $key->payment_detail_id;
                $journal_entry->branch_id = $key->branch_id;
                $journal_entry->currency_id = $key->currency_id;
                $journal_entry->chart_of_account_id = $key->chart_of_account_id;
                $journal_entry->transaction_type = $key->transaction_type;
                $journal_entry->date = date("Y-m-d");
                $date = explode('-', date("Y-m-d"));
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $key->credit;
                $journal_entry->reference = $key->reference;
                $journal_entry->manual_entry = $key->manual_entry;
                $journal_entry->notes = $key->notes;
                $journal_entry->save();
            } else {
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = $transaction_number;
                $journal_entry->payment_detail_id = $key->payment_detail_id;
                $journal_entry->branch_id = $key->branch_id;
                $journal_entry->currency_id = $key->currency_id;
                $journal_entry->chart_of_account_id = $key->chart_of_account_id;
                $journal_entry->transaction_type = $key->transaction_type;
                $journal_entry->date = date("Y-m-d");
                $date = explode('-', date("Y-m-d"));
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $key->debit;
                $journal_entry->reference = $key->reference;
                $journal_entry->manual_entry = $key->manual_entry;
                $journal_entry->notes = $key->notes;
                $journal_entry->save();
            }
        }
        activity()->log('Reverse Journal Entry');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back()->with("transaction_number", $transaction_number);
    }
}
