<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\CustomField\Entities\CustomField;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsTransaction;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Wallet\Events\TransactionUpdated;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:wallet.wallets.index'])->only(['index', 'show', 'get_wallets']);
        $this->middleware(['permission:wallet.wallets.create'])->only(['create', 'store']);
        $this->middleware(['permission:wallet.wallets.edit'])->only(['edit', 'update', 'change_savings_officer']);
        $this->middleware(['permission:wallet.wallets.destroy'])->only(['destroy']);
        $this->middleware(['permission:wallet.wallets.approve_savings'])->only(['approve_savings', 'undo_approval', 'reject_savings', 'undo_rejection']);
        $this->middleware(['permission:wallet.wallets.activate_savings'])->only(['activate_savings', 'undo_activation']);
        $this->middleware(['permission:wallet.wallets.withdraw_savings'])->only(['withdraw_savings', 'undo_withdrawn']);
        $this->middleware(['permission:wallet.wallets.inactive_savings'])->only(['inactive_savings', 'undo_inactive']);
        $this->middleware(['permission:wallet.wallets.dormant_savings'])->only(['dormant_savings', 'undo_dormant']);
        $this->middleware(['permission:wallet.wallets.close_savings'])->only(['close_savings', 'undo_closed']);
        $this->middleware(['permission:wallet.wallets.transactions.create'])->only(['create_transaction', 'store_transaction', 'create_deposit', 'store_deposit', 'create_savings_linked_charge', 'store_savings_linked_charge', 'pay_charge', 'store_pay_charge', 'create_withdrawal', 'store_withdrawal']);
        $this->middleware(['permission:wallet.wallets.transactions.edit'])->only(['waive_interest', 'update_transaction', 'edit_transaction', 'waive_charge', 'edit_deposit', 'update_deposit', 'edit_withdrawal', 'update_withdrawal']);
        $this->middleware(['permission:wallet.wallets.transactions.destroy'])->only(['destroy_transaction', 'reverse_transaction']);
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
        $status = $request->status;
        $client_id = $request->client_id;
        $currency_id = $request->currency_id;
        $branch_id = $request->branch_id;
        $data = Wallet::leftJoin("clients", "clients.id", "wallets.client_id")
            ->leftJoin("branches", "branches.id", "wallets.branch_id")
            ->leftJoin("users", "users.id", "wallets.created_by_id")
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($status, function ($query) use ($status) {
                $query->where("wallets.status", $status);
            })
            ->when($client_id, function ($query) use ($client_id) {
                $query->where("wallets.client_id", $client_id);
            })
            ->when($currency_id, function ($query) use ($currency_id) {
                $query->where("wallets.currency_id", $currency_id);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("wallets.branch_id", $branch_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
                $query->orWhere('wallets.id', 'like', "%$search%");
            })
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) created_by,wallets.*,branches.name branch")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('wallet::wallet.index', compact('data'));
    }

    public function get_wallets(Request $request)
    {

        $status = $request->status;
        $client_id = $request->client_id;
        $currency_id = $request->currency_id;
        $branch_id = $request->branch_id;

        $query = DB::table("wallets")
            ->leftJoin("clients", "clients.id", "wallets.client_id")
            ->leftJoin("branches", "branches.id", "wallets.branch_id")
            ->leftJoin("users", "users.id", "wallets.created_by_id")
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) created_by,wallets.*,branches.name branch")
            ->when($status, function ($query) use ($status) {
                $query->where("wallets.status", $status);
            })->when($client_id, function ($query) use ($client_id) {
                $query->where("wallets.client_id", $client_id);
            })->when($currency_id, function ($query) use ($currency_id) {
                $query->where("wallets.currency_id", $currency_id);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("wallets.branch_id", $branch_id);
            })->groupBy("wallets.id");
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
        })->editColumn('balance', function ($data) {
            return number_format($data->balance, $data->decimals);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('wallet::general.pending', 1) . ' ' . trans_choice('savings::general.approval', 1) . '</span>';
            }
            if ($data->status == 'submitted') {
                return '<span class="label label-warning">' . trans_choice('wallet::general.pending_approval', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('wallet::general.awaiting_activation', 1) . '</span>';
            }
            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('wallet::general.active', 1) . '</span>';
            }
            if ($data->status == 'rejected') {
                return '<span class="label label-danger">' . trans_choice('wallet::general.rejected', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('wallet::general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'dormant') {
                return '<span class="label label-warning">' . trans_choice('savings::general.dormant', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('wallet::general.closed', 1) . '</span>';
            }
            if ($data->status == 'inactive') {
                return '<span class="label label-warning">' . trans_choice('wallet::general.inactive', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('wallet/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('wallet/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'client', 'action', 'status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->get();
        $branches = Branch::where('active', 1)->get();
        $currencies = Currency::get();
        \JavaScript::put([
            'clients' => $clients,
            'branches' => $branches,
            'currencies' => $currencies,
        ]);
        $custom_fields = CustomField::where('category', 'add_wallet')->where('active', 1)->get();
        return theme_view('wallet::wallet.create', compact('custom_fields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (Wallet::where('client_id', $request->client_id)->where('currency_id', $request->currency_id)->count() > 0) {
            \flash(trans_choice("wallet::general.wallet_already_exist", 1))->warning()->important();
            return redirect()->back();
        }
        $request->validate([
            'client_id' => ['required'],
            'currency_id' => ['required'],
            'branch_id' => ['required'],
            'submitted_on_date' => ['required', 'date'],
        ]);
        $wallet = new Wallet();
        $wallet->submitted_by_user_id = Auth::id();
        $wallet->created_by_id = Auth::id();
        $wallet->client_id = $request->client_id;
        $wallet->branch_id = $request->branch_id;
        $wallet->currency_id = $request->currency_id;
        $wallet->submitted_on_date = $request->submitted_on_date;
        $wallet->status = 'active';
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Create Wallet');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $wallet = Wallet::find($id);
        $custom_fields = CustomField::where('category', 'add_wallet')->where('active', 1)->get();
        return theme_view('wallet::wallet.show', compact('wallet', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $wallet = Wallet::find($id);
        return theme_view('wallet::wallet.edit');
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
            'client_id' => ['required'],
            'currency_id' => ['required'],
            'branch_id' => ['required'],
            'submitted_on_date' => ['required', 'date'],
        ]);
        $wallet = Wallet::find($id);
    }

    public function approve_wallet(Request $request, $id)
    {
        $request->validate([
            'approved_on_date' => ['required', 'date'],
        ]);
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->approved_by_user_id = Auth::id();
        $wallet->approved_on_date = $request->approved_on_date;
        $wallet->status = 'approved';
        $wallet->approved_notes = $request->approved_notes;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Approve Wallet');
        //fire wallet status changed event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function undo_approval(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->approved_by_user_id = null;
        $wallet->approved_on_date = null;
        $wallet->status = 'pending';
        $wallet->approved_notes = null;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Undo Wallet Approval');
        //fire wallet status changed event

        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function activate_wallet(Request $request, $id)
    {
        $request->validate([
            'activated_on_date' => ['required', 'date'],
        ]);
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->activated_by_user_id = Auth::id();
        $wallet->activated_on_date = $request->activated_on_date;
        $wallet->status = 'active';
        $wallet->activated_notes = $request->activated_notes;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Activate Wallet');
        //fire wallet status changed event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function undo_activation(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->activated_by_user_id = null;
        $wallet->activated_on_date = null;
        $wallet->status = 'approved';
        $wallet->activated_notes = null;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Undo Wallet Activation');
        //fire wallet status changed event

        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function reject_wallet(Request $request, $id)
    {
        $request->validate([
            'rejected_notes' => ['required'],
        ]);
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->rejected_by_user_id = Auth::id();
        $wallet->rejected_on_date = $request->rejected_on_date;
        $wallet->status = 'rejected';
        $wallet->rejected_notes = $request->rejected_notes;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Reject Wallet');
        //fire wallet status changed event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function undo_rejection(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->rejected_by_user_id = null;
        $wallet->rejected_on_date = null;
        $wallet->status = 'pending';
        $wallet->rejected_notes = null;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Undo Wallet Rejection');
        //fire wallet status changed event

        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function close_wallet(Request $request, $id)
    {
        $request->validate([
            'closed_on_date' => ['required', 'date'],
        ]);
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->closed_by_user_id = Auth::id();
        $wallet->closed_on_date = $request->closed_on_date;
        $wallet->status = 'closed';
        $wallet->closed_notes = $request->closed_notes;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Close Wallet');
        //fire wallet status changed event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function undo_close(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        $previous_status = $wallet->status;
        $wallet->closed_by_user_id = null;
        $wallet->closed_on_date = null;
        $wallet->status = 'active';
        $wallet->closed_notes = null;
        $wallet->save();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Undo Wallet Close');
        //fire wallet status changed event

        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function create_deposit($id)
    {
        $wallet = Wallet::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('wallet::wallet.create_deposit', compact('id', 'payment_types'));
    }

    public function store_deposit(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $wallet = Wallet::find($id);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'wallet_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->created_by_id = Auth::id();
        $wallet_transaction->wallet_id = $wallet->id;
        $wallet_transaction->branch_id = $wallet->branch_id;
        $wallet_transaction->payment_detail_id = $payment_detail->id;
        $wallet_transaction->name = trans_choice('wallet::general.deposit', 1);
        $wallet_transaction->transaction_type = 'deposit';
        $wallet_transaction->submitted_on = $request->date;
        $wallet_transaction->created_on = date("Y-m-d");
        $wallet_transaction->reversible = 1;
        $wallet_transaction->amount = $request->amount;
        $wallet_transaction->credit = $request->amount;
        $wallet_transaction->save();
        $wallet->balance = $wallet->balance + $request->amount;
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Create Wallet Deposit');
        //fire transaction updated event
        event(new TransactionUpdated($wallet));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function create_loan_transfer($id)
    {
        $wallet = Wallet::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        if (empty($wallet->client->loans)) {
            \flash(trans_choice("wallet::general.no_loans_found", 1))->warning()->important();
            return redirect('wallet/' . $wallet->id . '/show');
        }
        return theme_view('wallet::wallet.create_loan_transfer', compact('wallet', 'payment_types'));
    }

    public function store_loan_transfer(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        if ($wallet->balance < $request->amount) {
            Flash::warning(trans('wallet::general.insufficient_funds'));
            return redirect()->back();
        }
        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
            'loan_id' => ['required'],
        ]);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'wallet_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->created_by_id = Auth::id();
        $wallet_transaction->wallet_id = $wallet->id;
        $wallet_transaction->branch_id = $wallet->branch_id;
        $wallet_transaction->payment_detail_id = $payment_detail->id;
        $wallet_transaction->name = trans_choice('wallet::general.loan_transfer', 1);
        $wallet_transaction->transaction_type = 'loan_transfer';
        $wallet_transaction->submitted_on = $request->date;
        $wallet_transaction->created_on = date("Y-m-d");
        $wallet_transaction->reversible = 1;
        $wallet_transaction->amount = $request->amount;
        $wallet_transaction->debit = $request->amount;
        $wallet_transaction->save();
        $wallet->balance = $wallet->balance - $request->amount;
        //fire transaction updated event
        event(new TransactionUpdated($wallet));
        $loan = Loan::with('loan_product')->find($request->loan_id);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'loan_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->payment_detail_id = $payment_detail->id;
        $loan_transaction->name = trans_choice('loan::general.repayment', 1);
        $loan_transaction->loan_transaction_type_id = 2;
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $request->amount;
        $loan_transaction->credit = $request->amount;
        $loan_transaction->save();
        activity()->on($wallet_transaction)
            ->withProperties(['id' => $wallet_transaction->id])
            ->log('Create Wallet Transfer');
        //fire transaction updated event
        event(new \Modules\Loan\Events\TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $id . '/show');
    }

    public function create_savings_transfer($id)
    {
        $wallet = Wallet::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        if (empty($wallet->client->loans)) {
            \flash(trans_choice("wallet::general.no_savings_found", 1))->warning()->important();
            return redirect('wallet/' . $wallet->id . '/show');
        }
        return theme_view('wallet::wallet.create_savings_transfer', compact('wallet', 'payment_types'));
    }

    public function store_savings_transfer(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        if ($wallet->balance < $request->amount) {
            \flash(trans_choice("wallet::general.insufficient_funds", 1))->warning()->important();
            return redirect()->back();
        }
        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
            'savings_id' => ['required'],
        ]);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'wallet_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->created_by_id = Auth::id();
        $wallet_transaction->wallet_id = $wallet->id;
        $wallet_transaction->branch_id = $wallet->branch_id;
        $wallet_transaction->payment_detail_id = $payment_detail->id;
        $wallet_transaction->name = trans_choice('wallet::general.savings_transfer', 1);
        $wallet_transaction->transaction_type = 'savings_transfer';
        $wallet_transaction->submitted_on = $request->date;
        $wallet_transaction->created_on = date("Y-m-d");
        $wallet_transaction->reversible = 1;
        $wallet_transaction->amount = $request->amount;
        $wallet_transaction->debit = $request->amount;
        $wallet_transaction->save();
        $wallet->balance = $wallet->balance - $request->amount;
        //fire transaction updated event
        event(new TransactionUpdated($wallet));
        $savings = Savings::with('savings_product')->find($request->savings_id);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'savings_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $savings_transaction = new SavingsTransaction();
        $savings_transaction->created_by_id = Auth::id();
        $savings_transaction->savings_id = $savings->id;
        $savings_transaction->branch_id = $savings->branch_id;
        $savings_transaction->payment_detail_id = $payment_detail->id;
        $savings_transaction->name = trans_choice('savings::general.deposit', 1);
        $savings_transaction->savings_transaction_type_id = 1;
        $savings_transaction->submitted_on = $request->date;
        $savings_transaction->created_on = date("Y-m-d");
        $savings_transaction->reversible = 1;
        $savings_transaction->amount = $request->amount;
        $savings_transaction->credit = $request->amount;
        $savings_transaction->save();
        activity()->on($wallet_transaction)
            ->withProperties(['id' => $wallet_transaction->id])
            ->log('Create Wallet Savings Transfer');
        //fire transaction updated event
        event(new \Modules\Savings\Events\TransactionUpdated($savings));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $id . '/show');
    }

    //transactions
    public function show_transaction($id)
    {
        $wallet_transaction = WalletTransaction::with('payment_detail')->with('wallet')->find($id);
        return theme_view('wallet::wallet_transaction.show', compact('wallet_transaction'));
    }

    public function pdf_transaction($id)
    {
        $wallet_transaction = WalletTransaction::with('payment_detail')->with('wallet')->find($id);
        $pdf = PDF::loadView(theme_view_file('wallet::wallet_transaction.pdf'), compact('wallet_transaction'));
        return $pdf->download(trans_choice('wallet::general.transaction', 1) . ' ' . trans_choice('core::general.detail', 2) . ".pdf");
    }

    public function print_transaction($id)
    {
        $wallet_transaction = WalletTransaction::with('payment_detail')->with('wallet')->find($id);
        return theme_view('wallet::wallet_transaction.print', compact('wallet_transaction'));
    }

    public function edit_transaction($id)
    {
        $wallet_transaction = WalletTransaction::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        $custom_fields = CustomField::where('category', 'add_transaction')->where('active', 1)->get();
        return theme_view('wallet::wallet_transaction.edit', compact('wallet_transaction', 'payment_types', 'custom_fields'));
    }

    public function update_transaction(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $wallet_transaction = WalletTransaction::find($id);
        $wallet = $wallet_transaction->wallet;
        if ($wallet_transaction->transaction_type == 'deposit') {
            $wallet_transaction->credit = $request->amount;
            $wallet->balance = $wallet->balance - $wallet_transaction->amount + $request->amount;
        } else {
            $wallet_transaction->debit = $request->amount;
            $wallet->balance = $wallet->balance + $wallet_transaction->amount - $request->amount;
        }
        $wallet->save();
        //payment details
        $payment_detail = PaymentDetail::find($wallet_transaction->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $wallet_transaction->submitted_on = $request->date;
        $wallet_transaction->amount = $request->amount;
        if ($wallet_transaction->transaction_type == 'deposit') {
            $wallet_transaction->credit = $request->amount;
        } else {
            $wallet_transaction->debit = $request->amount;
        }

        $wallet_transaction->save();
        activity()->on($wallet_transaction)
            ->withProperties(['id' => $wallet_transaction->id])
            ->log('Update Wallet Transaction');
        event(new TransactionUpdated($wallet));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    public function reverse_transaction(Request $request, $id)
    {

        $wallet_transaction = WalletTransaction::find($id);
        $wallet = $wallet_transaction->wallet;
        if ($wallet_transaction->transaction_type == 'deposit') {
            $wallet_transaction->debit = $wallet_transaction->credit;
            $wallet->balance = $wallet->balance - $wallet_transaction->amount;
        } else {
            $wallet_transaction->debit = $wallet_transaction->credit;
            $wallet->balance = $wallet->balance + $wallet_transaction->amount;
        }
        $wallet_transaction->amount = 0;
        $wallet_transaction->reversed = 1;
        $wallet_transaction->save();
        activity()->on($wallet_transaction)
            ->withProperties(['id' => $wallet_transaction->id])
            ->log('Reverse Wallet Transaction');
        //fire transaction updated event
        event(new TransactionUpdated($wallet_transaction));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet/' . $wallet->id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $wallet = Wallet::find($id);
        WalletTransaction::where('wallet_id', $id)->delete();
        $wallet->delete();
        activity()->on($wallet)
            ->withProperties(['id' => $wallet->id])
            ->log('Delete Wallet');
        //fire wallet status changed event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('wallet');
    }
}
