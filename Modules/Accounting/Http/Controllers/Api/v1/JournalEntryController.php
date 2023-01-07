<?php

namespace Modules\Accounting\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Core\Entities\PaymentDetail;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
        $limit = $request->limit ? $request->limit : 20;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $chart_of_account_id = $request->chart_of_account_id;

        $data = DB::table("journal_entries")->leftJoin("branches", "branches.id", "journal_entries.branch_id")->leftJoin("chart_of_accounts", "chart_of_accounts.id", "journal_entries.chart_of_account_id")->leftJoin("users", "users.id", "journal_entries.created_by_id")->when($end_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween("journal_entries.date", [$start_date, $end_date]);
        })->when($branch_id, function ($query) use ($branch_id) {
            $query->where("journal_entries.branch_id", $branch_id);
        })->when($chart_of_account_id, function ($query) use ($chart_of_account_id) {
            $query->where("journal_entries.chart_of_account_id", $chart_of_account_id);
        })->selectRaw("journal_entries.id,journal_entries.date,journal_entries.debit,journal_entries.credit,journal_entries.transaction_number,branches.name branch,chart_of_accounts.account_type,chart_of_accounts.name account_name,concat(users.first_name,' ',users.last_name) created_by")
            ->paginate($limit);
        return response()->json([$data]);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'branch_id' => ['required'],
            'currency_id' => ['required'],
            'amount' => ['required'],
            'debit' => ['required'],
            'credit' => ['required'],
            'date' => ['required', 'date']
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
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
            $data = JournalEntry::where('transaction_number', $transaction_number)->with('chart_of_account')->with('branch')->get();
            return response()->json(['data' => $data, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $data = JournalEntry::where('transaction_number', $id)->with('chart_of_account')->with('branch')->get();
        return response()->json(['data' => $data]);
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
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_saved", 1)]);

    }
}
