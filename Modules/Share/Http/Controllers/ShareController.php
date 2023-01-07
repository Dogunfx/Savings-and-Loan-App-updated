<?php

namespace Modules\Share\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\CustomField\Entities\CustomField;
use Modules\Savings\Entities\Savings;
use Modules\Share\Entities\Share;
use Modules\Share\Entities\ShareCharge;
use Modules\Share\Entities\ShareLinkedCharge;
use Modules\Share\Entities\ShareProduct;
use Modules\Share\Entities\ShareTransaction;
use Modules\Share\Events\ShareStatusChanged;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class ShareController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:share.shares.index'])->only(['index', 'show']);
        $this->middleware(['permission:share.shares.create'])->only(['create', 'store']);
        $this->middleware(['permission:share.shares.edit'])->only(['edit', 'update', 'change_share_officer']);
        $this->middleware(['permission:share.shares.destroy'])->only(['destroy']);
        $this->middleware(['permission:share.shares.approve_shares'])->only(['approve_share', 'undo_approval', 'reject_share', 'undo_rejection']);
        $this->middleware(['permission:share.shares.activate_shares'])->only(['activate_share', 'undo_activation']);
        $this->middleware(['permission:share.shares.withdraw_shares'])->only(['withdraw_share', 'undo_withdrawn']);
        $this->middleware(['permission:share.shares.inactive_shares'])->only(['inactive_share', 'undo_inactive']);
        $this->middleware(['permission:share.shares.dormant_shares'])->only(['dormant_share', 'undo_dormant']);
        $this->middleware(['permission:share.shares.close_shares'])->only(['close_share', 'undo_closed']);
        $this->middleware(['permission:share.shares.transactions.create'])->only(['create_transaction', 'store_transaction', 'create_deposit', 'store_deposit', 'create_share_linked_charge', 'store_share_linked_charge', 'pay_charge', 'store_pay_charge', 'create_withdrawal', 'store_withdrawal']);
        $this->middleware(['permission:share.shares.transactions.edit'])->only(['waive_interest', 'update_transaction', 'edit_transaction', 'waive_charge', 'edit_deposit', 'update_deposit', 'edit_withdrawal', 'update_withdrawal']);
        $this->middleware(['permission:share.shares.transactions.destroy'])->only(['destroy_transaction', 'reverse_transaction']);
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
        $data = Share::leftJoin("clients", "clients.id", "shares.client_id")
            ->leftJoin("share_products", "share_products.id", "shares.share_product_id")
            ->leftJoin("savings", "savings.id", "shares.savings_id")
            ->leftJoin("branches", "branches.id", "shares.branch_id")
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('shares.account_number', 'like', "%$search%");
                $query->orWhere('shares.id', 'like', "%$search%");
                $query->orWhere('shares.external_id', 'like', "%$search%");
                $query->orWhere('shares_products.name', 'like', "%$search%");
                $query->orWhere('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
            })
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,savings.account_number savings_account_number,shares.id,shares.client_id,shares.savings_id,shares.activated_on_date,share_products.name share_product,shares.status,shares.decimals,branches.name branch, shares.total_shares")
            ->paginate($perPage)
            ->appends($request->input());

        return theme_view('share::share.index', compact('data'));
    }

    public function get_shares(Request $request)
    {

        $status = $request->status;
        $client_id = $request->client_id;
        $share_officer_id = $request->share_officer_id;

        $query = DB::table("shares")
            ->leftJoin("clients", "clients.id", "shares.client_id")
            ->leftJoin("share_products", "share_products.id", "shares.share_product_id")
            ->leftJoin("savings", "savings.id", "shares.savings_id")
            ->leftJoin("branches", "branches.id", "shares.branch_id")
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,savings.account_number savings_account_number,shares.id,shares.client_id,shares.savings_id,shares.activated_on_date,share_products.name share_product,shares.status,shares.decimals,branches.name branch, shares.total_shares")
            ->when($status, function ($query) use ($status) {
                $query->where("shares.status", $status);
            })
            ->when($client_id, function ($query) use ($client_id) {
                $query->where("shares.client_id", $client_id);
            })
            ->when($share_officer_id, function ($query) use ($share_officer_id) {
                $query->where("shares.share_officer_id", $share_officer_id);
            })
            ->groupBy("shares.id");
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
        })->editColumn('total_shares', function ($data) {
            return number_format($data->total_shares);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('share::general.pending', 1) . ' ' . trans_choice('share::general.approval', 1) . '</span>';
            }
            if ($data->status == 'submitted') {
                return '<span class="label label-warning">' . trans_choice('share::general.pending_approval', 1) . '</span>';
            }
            if ($data->status == 'overpaid') {
                return '<span class="label label-warning">' . trans_choice('share::general.overpaid', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('share::general.awaiting_activation', 1) . '</span>';
            }
            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('share::general.active', 1) . '</span>';
            }
            if ($data->status == 'rejected') {
                return '<span class="label label-danger">' . trans_choice('share::general.rejected', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('share::general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'dormant') {
                return '<span class="label label-warning">' . trans_choice('share::general.dormant', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('share::general.closed', 1) . '</span>';
            }
            if ($data->status == 'inactive') {
                return '<span class="label label-warning">' . trans_choice('share::general.inactive', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('share/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('share/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->editColumn('savings_account', function ($data) {
            return '<a href="' . url('savings/' . $data->savings_id . '/show') . '" class="">' . $data->savings_id . '</a>';

        })->rawColumns(['id', 'client', 'action', 'status', 'savings_account'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->get();
        $share_products = ShareProduct::with('charges')->with('charges.charge')->where('active', 1)->get();
        $savings = Savings::where('status', 'active')
            ->get();
        $share_charges =ShareCharge::where('active', 1)->get();
        foreach (ShareCharge::where('active', 1)->get() as $key) {

            //charge option
            if ($key->share_charge_option_id == 1) {
                $key->share_charge_option_id = trans_choice('share::general.flat', 1);
            }
            if ($key->share_charge_option_id == 2) {
                $key->share_charge_option_id = trans_choice('share::general.percentage_of_amount', 1);
            }

            //charge type
            if ($key->share_charge_type_id == 1) {
                $key->share_charge_type_id = trans_choice('share::general.share_account_activation', 1);
            }
            if ($key->share_charge_type_id == 2) {
                $key->share_charge_type_id = trans_choice('share::general.share_purchase', 1);
            }
            if ($key->share_charge_type_id == 3) {
                $key->share_charge_type_id = trans_choice('share::general.share_redeem', 1);
            }

        }
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        \JavaScript::put([
            'clients' => $clients,
            'share_products' => $share_products,
            'share_charges' => $share_charges,
            'users' => $users,
            'savings' => $savings,
        ]);
        $custom_fields = CustomField::where('category', 'add_share')->where('active', 1)->get();
        return theme_view('share::share.create', compact('clients', 'share_products', 'users', 'custom_fields', 'savings'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => ['required'],
            'share_product_id' => ['required'],
            'savings_id' => ['required'],
            'total_shares' => ['required', 'numeric'],
            'lockin_period' => ['required', 'numeric'],
            'lockin_type' => ['required'],
            'submitted_on_date' => ['required', 'date'],
            'application_date' => ['required', 'date'],
        ]);
        $share_product = ShareProduct::find($request->share_product_id);
        $client = Client::find($request->client_id);
        $share = new Share();
        $share->currency_id = $share_product->currency_id;
        $share->created_by_id = Auth::id();
        $share->client_id = $request->client_id;
        $share->share_product_id = $request->share_product_id;
        $share->share_officer_id = $request->share_officer_id;
        $share->savings_id = $request->savings_id;
        $share->branch_id = $client->branch_id;
        $share->total_shares = $request->total_shares;
        $share->nominal_price = $share_product->nominal_price;
        $share->minimum_active_period = $request->minimum_active_period;
        $share->minimum_active_period_type = $request->minimum_active_period_type;
        $share->decimals = $share_product->decimals;
        $share->lockin_period = $request->lockin_period;
        $share->lockin_type = $request->lockin_type;
        $share->submitted_on_date = $request->submitted_on_date;
        $share->application_date = $request->application_date;
        $share->submitted_by_user_id = Auth::id();
        $share->save();
        //save charges
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $share_charge = ShareCharge::find($key);
                $share_linked_charge = new ShareLinkedCharge();
                $share_linked_charge->share_id = $share->id;
                $share_linked_charge->name = $share_charge->name;
                $share_linked_charge->share_charge_id = $key;
                if ($share_linked_charge->allow_override == 1) {
                    $share_linked_charge->amount = $value;
                } else {
                    $share_linked_charge->amount = $share_charge->amount;
                }
                $share_linked_charge->share_charge_type_id = $share_charge->share_charge_type_id;
                $share_linked_charge->share_charge_option_id = $share_charge->share_charge_option_id;
                $share_linked_charge->save();
            }
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Create Share');
        custom_fields_save_form('add_share', $request, $share->id);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $payment_types = PaymentType::where('active', 1)->get();
        $share = Share::with('transactions')->with('charges')->with('client')->with('share_product')->find($id);
        $custom_fields = CustomField::where('category', 'add_share')->where('active', 1)->get();
        return theme_view('share::share.show', compact('share', 'payment_types', 'users', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $share = Share::with('charges')->with('share_product')->with('share_product.charges')->with('share_product.charges.charge')->find($id);
        $clients = Client::where('status', 'active')->get();
        $share_products =ShareProduct::with('charges')->with('charges.charge')->where('active', 1)->get();
        $charges_list = [];
        $temp_charges = [];
        foreach ($share->charges as $key) {
            $temp_charges[] = $key->share_charge_id;
        }
        $savings = Savings::where('status', 'active')
            ->get();
        $share_charges =ShareCharge::where('active', 1)->get();
        foreach (ShareCharge::where('active', 1)->get() as $key) {
            //charge option
            if ($key->share_charge_option_id == 1) {
                $key->share_charge_option_id = trans_choice('share::general.flat', 1);
            }
            if ($key->share_charge_option_id == 2) {
                $key->share_charge_option_id = trans_choice('share::general.percentage_of_amount', 1);
            }
            //charge type
            if ($key->share_charge_type_id == 1) {
                $key->share_charge_type_id = trans_choice('share::general.share_account_activation', 1);
            }
            if ($key->share_charge_type_id == 2) {
                $key->share_charge_type_id = trans_choice('share::general.share_purchase', 1);
            }
            if ($key->share_charge_type_id == 3) {
                $key->share_charge_type_id = trans_choice('share::general.share_redeem', 1);
            }
            if (in_array($key->id, $temp_charges)) {
                $charges_list[] = $key;
            }
        }
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        \JavaScript::put([
            'clients' => $clients,
            'share_products' => $share_products,
            'share_charges' => $share_charges,
            'users' => $users,
            'charges_list' => $charges_list,
            'savings' => $savings,

        ]);
        $custom_fields = CustomField::where('category', 'add_share')->where('active', 1)->get();
        return theme_view('share::share.edit', compact('share', 'users', 'clients', 'share_products', 'charges_list', 'custom_fields'));
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
            'share_product_id' => ['required'],
            'savings_id' => ['required'],
            'total_shares' => ['required', 'numeric'],
            'lockin_period' => ['required', 'numeric'],
            'lockin_type' => ['required'],
            'submitted_on_date' => ['required', 'date'],
            'application_date' => ['required', 'date'],
        ]);
        $share_product = ShareProduct::find($request->share_product_id);
        $client = Client::find($request->client_id);
        $share = Share::find($id);
        $share->currency_id = $share_product->currency_id;
        $share->client_id = $request->client_id;
        $share->share_product_id = $request->share_product_id;
        $share->share_officer_id = $request->share_officer_id;
        $share->savings_id = $request->savings_id;
        $share->branch_id = $client->branch_id;
        $share->total_shares = $request->total_shares;
        $share->nominal_price = $share_product->nominal_price;
        $share->minimum_active_period = $request->minimum_active_period;
        $share->minimum_active_period_type = $request->minimum_active_period_type;
        $share->decimals = $share_product->decimals;
        $share->lockin_period = $request->lockin_period;
        $share->lockin_type = $request->lockin_type;
        $share->submitted_on_date = $request->submitted_on_date;
        $share->application_date = $request->application_date;
        $share->save();
        //save charges
        ShareLinkedCharge::where('share_id', $id)->delete();
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $share_charge = ShareCharge::find($key);
                $share_linked_charge = new ShareLinkedCharge();
                $share_linked_charge->share_id = $share->id;
                $share_linked_charge->name = $share_charge->name;
                $share_linked_charge->share_charge_id = $key;
                if ($share_linked_charge->allow_override == 1) {
                    $share_linked_charge->amount = $value;
                } else {
                    $share_linked_charge->amount = $share_charge->amount;
                }
                $share_linked_charge->share_charge_type_id = $share_charge->share_charge_type_id;
                $share_linked_charge->share_charge_option_id = $share_charge->share_charge_option_id;
                $share_linked_charge->save();
            }
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Update Share');
        custom_fields_save_form('add_share', $request, $share->id);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $share = Share::find($id);
        $share->delete();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Delete Share');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function change_share_officer(Request $request, $id)
    {

        $request->validate([
            'share_officer_id' => ['required'],
        ]);
        $share = share::find($id);
        $previous_share_officer_id = $share->share_officer_id;
        $share->share_officer_id = $request->share_officer_id;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Change Share Officer');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function approve_share(Request $request, $id)
    {

        $request->validate([
            'approved_on_date' => ['required', 'date'],
        ]);
        $share = Share::find($id);
        $previous_status = $share->status;
        $share->approved_by_user_id = Auth::id();
        $share->approved_on_date = $request->approved_on_date;
        $share->status = 'approved';
        $share->approved_notes = $request->approved_notes;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Approve Shares');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_approval(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->approved_by_user_id = null;
        $share->approved_on_date = null;
        $share->status = 'submitted';
        $share->approved_notes = null;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Approval');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function reject_share(Request $request, $id)
    {

        $request->validate([
            'rejected_notes' => ['required'],
        ]);
        $share = Share::find($id);
        $previous_status = $share->status;
        $share->rejected_by_user_id = Auth::id();
        $share->rejected_on_date = date("Y-m-d");
        $share->status = 'rejected';
        $share->rejected_notes = $request->rejected_notes;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Reject Share');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_rejection(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->rejected_by_user_id = null;
        $share->rejected_on_date = null;
        $share->status = 'submitted';
        $share->rejected_notes = null;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Rejection');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function withdraw_share(Request $request, $id)
    {

        $request->validate([
            'withdrawn_notes' => ['required'],
        ]);
        $share = Share::find($id);
        $previous_status = $share->status;
        $share->withdrawn_by_user_id = Auth::id();
        $share->withdrawn_on_date = date("Y-m-d");
        $share->status = 'withdrawn';
        $share->withdrawn_notes = $request->withdrawn_notes;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Withdraw Share');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_withdrawn(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->withdrawn_by_user_id = null;
        $share->withdrawn_on_date = null;
        $share->status = 'submitted';
        $share->withdrawn_notes = null;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Withdrawal');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function activate_share(Request $request, $id)
    {

        $request->validate([
            'activated_on_date' => ['required', 'date'],
        ]);

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->activated_by_user_id = Auth::id();
        $share->activated_on_date = $request->activated_on_date;
        $share->status = 'active';
        $share->activated_notes = $request->activated_notes;
        $share->save();
        $share_transaction = new ShareTransaction();
        $share_transaction->created_by_id = Auth::id();
        $share_transaction->branch_id = $share->branch_id;
        $share_transaction->share_id = $share->id;
        $share_transaction->name = trans_choice('share::general.share_purchase', 1);
        $share_transaction->share_transaction_type_id = 1;
        $share_transaction->submitted_on = $share->activated_on_date;
        $share_transaction->created_on = date("Y-m-d");
        $share_transaction->amount = $share->total_shares * $share->share_product->nominal_price;
        $share_transaction->credit = $share->total_shares * $share->share_product->nominal_price;
        $share_transaction->total_shares = $share->total_shares;
        $share_transaction->reversible = 1;
        $share_transaction->save();
        if ($share->share_product->accounting_rule == 'cash') {
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
            $journal_entry->branch_id = $share->branch_id;
            $journal_entry->currency_id = $share->currency_id;
            $journal_entry->chart_of_account_id = $share->share_product->share_suspense_control_chart_of_account_id;
            $journal_entry->transaction_type = 'share_purchase';
            $journal_entry->date = $share->activated_on_date;
            $date = explode('-', $share->activated_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $share_transaction->amount;
            $journal_entry->reference = $share->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
            $journal_entry->branch_id = $share->branch_id;
            $journal_entry->currency_id = $share->currency_id;
            $journal_entry->chart_of_account_id = $share->share_product->share_reference_chart_of_account_id;
            $journal_entry->transaction_type = 'share_purchase';
            $journal_entry->date = $share->activated_on_date;
            $date = explode('-', $share->activated_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $share_transaction->amount;
            $journal_entry->reference = $share->id;
            $journal_entry->save();
        }
        //charges
        $total_amount = $share->total_shares * $share->share_product->nominal_price;
        foreach ($share->charges->whereIn('share_charge_type_id', [1, 2]) as $key) {
            $amount = 0;
            if ($key->share_charge_option_id == 1) {
                $amount = $key->amount;
            }
            if ($key->share_charge_option_id == 2) {
                $amount = round($key->amount * $total_amount / 100, $share->decimals);
            }
            $share_transaction = new ShareTransaction();
            $share_transaction->created_by_id = Auth::id();
            $share_transaction->share_id = $share->id;
            $share_transaction->branch_id = $share->branch_id;
            $share_transaction->name = trans_choice('share::general.apply', 1) . ' ' . trans_choice('share::general.charge', 1);
            $share_transaction->share_transaction_type_id = 4;
            $share_transaction->reversible = 1;
            $share_transaction->submitted_on = $share->activated_on_date;
            $share_transaction->created_on = date("Y-m-d");
            $share_transaction->amount = $amount;
            $share_transaction->debit = $amount;
            $share_transaction->share_linked_charge_id = $key->id;
            $share_transaction->save();
            if ($share->share_product->accounting_rule == 'cash') {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
                $journal_entry->branch_id = $share->branch_id;
                $journal_entry->currency_id = $share->currency_id;
                $journal_entry->chart_of_account_id = $share->share_product->share_suspense_control_chart_of_account_id;
                $journal_entry->transaction_type = 'share_pay_charge';
                $journal_entry->date = $share->activated_on_date;
                $date = explode('-', $share->activated_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $amount;
                $journal_entry->reference = $share->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
                $journal_entry->branch_id = $share->branch_id;
                $journal_entry->currency_id = $share->currency_id;
                $journal_entry->chart_of_account_id = $share->share_product->income_from_fees_chart_of_account_id;
                $journal_entry->transaction_type = 'share_pay_charge';
                $journal_entry->date = $share->activated_on_date;
                $date = explode('-', $share->activated_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $amount;
                $journal_entry->reference = $share->id;
                $journal_entry->save();
            }
            $key->share_transaction_id = $share_transaction->id;
            $key->calculated_amount = $key->amount;
            $key->paid_amount = $key->amount;
            $key->is_paid = 1;
            $key->save();
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Activate Share');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_activation(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->activated_by_user_id = null;
        $share->activated_on_date = null;
        $share->status = 'approved';
        $share->activated_notes = null;
        $share->save();
        $transactions = ShareTransaction::where('share_id', $share->id)->get();
        foreach ($transactions as $transaction) {
            JournalEntry::where('transaction_number', 'Sh' . $transaction->id)->delete();
            $transaction->delete();
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Activation');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function redeem_share(Request $request, $id)
    {
        $share = Share::find($id);
        $request->validate([
            'redeem_total_shares' => ['required', 'lte:' . $share->total_shares],
            'redeem_on_date' => ['required', 'date'],
        ], [
            'redeem_total_shares.lte' => trans_choice('share::general.redeem_shares_cannot_be_greater_than_approved_shares', 1)
        ]);
        $share->total_shares = $share->total_shares - $request->redeem_total_shares;
        $share->save();
        $share_transaction = new ShareTransaction();
        $share_transaction->created_by_id = Auth::id();
        $share_transaction->branch_id = $share->branch_id;
        $share_transaction->share_id = $share->id;
        $share_transaction->name = trans_choice('share::general.share_redeem', 1);
        $share_transaction->share_transaction_type_id = 2;
        $share_transaction->submitted_on = $request->redeem_on_date;
        $share_transaction->created_on = date("Y-m-d");
        $share_transaction->amount = $request->redeem_total_shares * $share->share_product->nominal_price;
        $share_transaction->credit = $request->redeem_total_shares * $share->share_product->nominal_price;
        $share_transaction->total_shares = $request->redeem_total_shares;
        $share_transaction->description = $request->notes;
        $share_transaction->reversible = 1;
        $share_transaction->save();
        $total_amount = $share_transaction->amount;
        if ($share->share_product->accounting_rule == 'cash') {
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
            $journal_entry->branch_id = $share->branch_id;
            $journal_entry->currency_id = $share->currency_id;
            $journal_entry->chart_of_account_id = $share->share_product->share_reference_chart_of_account_id;
            $journal_entry->transaction_type = 'share_redeem';
            $journal_entry->date = $request->redeem_on_date;
            $date = explode('-', $request->redeem_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $share_transaction->amount;
            $journal_entry->reference = $share->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
            $journal_entry->branch_id = $share->branch_id;
            $journal_entry->currency_id = $share->currency_id;
            $journal_entry->chart_of_account_id = $share->share_product->share_suspense_control_chart_of_account_id;
            $journal_entry->transaction_type = 'share_redeem';
            $journal_entry->date = $request->redeem_on_date;
            $date = explode('-', $request->redeem_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $share_transaction->amount;
            $journal_entry->reference = $share->id;
            $journal_entry->save();
        }
        foreach ($share->charges->where('share_charge_type_id', 3) as $key) {
            $amount = 0;
            if ($key->share_charge_option_id == 1) {
                $amount = $key->amount;
            }
            if ($key->share_charge_option_id == 2) {
                $amount = round($key->amount * $total_amount / 100, $share->decimals);
            }
            $share_transaction = new ShareTransaction();
            $share_transaction->created_by_id = Auth::id();
            $share_transaction->share_id = $share->id;
            $share_transaction->branch_id = $share->branch_id;
            $share_transaction->name = trans_choice('share::general.apply', 1) . ' ' . trans_choice('share::general.charge', 1);
            $share_transaction->share_transaction_type_id = 4;
            $share_transaction->reversible = 1;
            $share_transaction->submitted_on = $request->redeem_on_date;
            $share_transaction->created_on = date("Y-m-d");
            $share_transaction->amount = $amount;
            $share_transaction->debit = $amount;
            $share_transaction->share_linked_charge_id = $key->id;
            $share_transaction->save();
            if ($share->share_product->accounting_rule == 'cash') {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
                $journal_entry->branch_id = $share->branch_id;
                $journal_entry->currency_id = $share->currency_id;
                $journal_entry->chart_of_account_id = $share->share_product->share_suspense_control_chart_of_account_id;
                $journal_entry->transaction_type = 'share_pay_charge';
                $journal_entry->date = $request->redeem_on_date;
                $date = explode('-', $request->redeem_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $amount;
                $journal_entry->reference = $share->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
                $journal_entry->branch_id = $share->branch_id;
                $journal_entry->currency_id = $share->currency_id;
                $journal_entry->chart_of_account_id = $share->share_product->income_from_fees_chart_of_account_id;
                $journal_entry->transaction_type = 'share_pay_charge';
                $journal_entry->date = $request->redeem_on_date;
                $date = explode('-', $request->redeem_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $amount;
                $journal_entry->reference = $share->id;
                $journal_entry->save();
            }
            $key->share_transaction_id = $share_transaction->id;
            $key->calculated_amount = $key->amount;
            $key->paid_amount = $key->amount;
            $key->is_paid = 1;
            $key->save();
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Redeem Share');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function purchase_share(Request $request, $id)
    {
        $share = Share::find($id);
        $request->validate([
            'purchase_total_shares' => ['required'],
            'purchase_on_date' => ['required', 'date'],
        ], [
            'purchase_total_shares.lte' => trans_choice('share::general.redeem_shares_cannot_be_greater_than_approved_shares', 1)
        ]);
        $share->total_shares = $share->total_shares + $request->purchase_total_shares;
        $share->save();
        $share_transaction = new ShareTransaction();
        $share_transaction->created_by_id = Auth::id();
        $share_transaction->branch_id = $share->branch_id;
        $share_transaction->share_id = $share->id;
        $share_transaction->name = trans_choice('share::general.share_purchase', 1);
        $share_transaction->share_transaction_type_id = 1;
        $share_transaction->submitted_on = $request->purchase_on_date;
        $share_transaction->created_on = date("Y-m-d");
        $share_transaction->amount = $request->purchase_total_shares * $share->share_product->nominal_price;
        $share_transaction->credit = $request->purchase_total_shares * $share->share_product->nominal_price;
        $share_transaction->total_shares = $request->purchase_total_shares;
        $share_transaction->description = $request->notes;
        $share_transaction->reversible = 1;
        $share_transaction->save();
        $total_amount = $share_transaction->amount;
        if ($share->share_product->accounting_rule == 'cash') {
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
            $journal_entry->branch_id = $share->branch_id;
            $journal_entry->currency_id = $share->currency_id;
            $journal_entry->chart_of_account_id = $share->share_product->share_suspense_control_chart_of_account_id;
            $journal_entry->transaction_type = 'share_purchase';
            $journal_entry->date = $request->purchase_on_date;
            $date = explode('-', $request->purchase_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $share_transaction->amount;
            $journal_entry->reference = $share->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
            $journal_entry->branch_id = $share->branch_id;
            $journal_entry->currency_id = $share->currency_id;
            $journal_entry->chart_of_account_id = $share->share_product->share_reference_chart_of_account_id;
            $journal_entry->transaction_type = 'share_purchase';
            $journal_entry->date = $request->purchase_on_date;
            $date = explode('-', $request->purchase_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $share_transaction->amount;
            $journal_entry->reference = $share->id;
            $journal_entry->save();
        }
        foreach ($share->charges->where('share_charge_type_id', 2) as $key) {
            $amount = 0;
            if ($key->share_charge_option_id == 1) {
                $amount = $key->amount;
            }
            if ($key->share_charge_option_id == 2) {
                $amount = round($key->amount * $total_amount / 100, $share->decimals);
            }
            $share_transaction = new ShareTransaction();
            $share_transaction->created_by_id = Auth::id();
            $share_transaction->share_id = $share->id;
            $share_transaction->branch_id = $share->branch_id;
            $share_transaction->name = trans_choice('share::general.apply', 1) . ' ' . trans_choice('share::general.charge', 1);
            $share_transaction->share_transaction_type_id = 4;
            $share_transaction->reversible = 1;
            $share_transaction->submitted_on = $request->purchase_on_date;
            $share_transaction->created_on = date("Y-m-d");
            $share_transaction->amount = $amount;
            $share_transaction->debit = $amount;
            $share_transaction->share_linked_charge_id = $key->id;
            $share_transaction->save();
            if ($share->share_product->accounting_rule == 'cash') {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
                $journal_entry->branch_id = $share->branch_id;
                $journal_entry->currency_id = $share->currency_id;
                $journal_entry->chart_of_account_id = $share->share_product->share_suspense_control_chart_of_account_id;
                $journal_entry->transaction_type = 'share_pay_charge';
                $journal_entry->date = $request->purchase_on_date;
                $date = explode('-', $request->purchase_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $amount;
                $journal_entry->reference = $share->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'Sh' . $share_transaction->id;
                $journal_entry->branch_id = $share->branch_id;
                $journal_entry->currency_id = $share->currency_id;
                $journal_entry->chart_of_account_id = $share->share_product->income_from_fees_chart_of_account_id;
                $journal_entry->transaction_type = 'share_pay_charge';
                $journal_entry->date = $request->purchase_on_date;
                $date = explode('-', $request->purchase_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $amount;
                $journal_entry->reference = $share->id;
                $journal_entry->save();
            }
            $key->share_transaction_id = $share_transaction->id;
            $key->calculated_amount = $key->amount;
            $key->paid_amount = $key->amount;
            $key->is_paid = 1;
            $key->save();
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Purchase Share');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function close_share(Request $request, $id)
    {

        $request->validate([
            'closed_notes' => ['required'],
        ]);
        $share = Share::find($id);
        $previous_status = $share->status;
        $share->closed_by_user_id = Auth::id();
        $share->closed_on_date = date("Y-m-d");
        $share->status = 'closed';
        $share->closed_notes = $request->closed_notes;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Close Share');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_closed(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->closed_by_user_id = null;
        $share->closed_on_date = null;
        $share->status = 'active';
        $share->closed_notes = null;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Closed');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function inactive_share(Request $request, $id)
    {

        $request->validate([
            'inactive_notes' => ['required'],
        ]);
        $share = Share::find($id);
        $previous_status = $share->status;
        $share->inactive_by_user_id = Auth::id();
        $share->inactive_on_date = date("Y-m-d");
        $share->status = 'inactive';
        $share->inactive_notes = $request->inactive_notes;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Inactivate Share');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_inactive(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->inactive_by_user_id = null;
        $share->inactive_on_date = null;
        $share->status = 'active';
        $share->inactive_notes = null;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Inactivation');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function dormant_share(Request $request, $id)
    {

        $request->validate([
            'dormant_notes' => ['required'],
        ]);
        $share = Share::find($id);
        $previous_status = $share->status;
        $share->dormant_by_user_id = Auth::id();
        $share->inactive_on_date = date("Y-m-d");
        $share->status = 'dormant';
        $share->dormant_notes = $request->dormant_notes;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Dormant Share');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function undo_dormant(Request $request, $id)
    {

        $share = Share::find($id);
        $previous_status = $share->status;
        $share->dormant_by_user_id = null;
        $share->dormant_on_date = null;
        $share->status = 'active';
        $share->dormant_notes = null;
        $share->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Undo Share Dormant');
        //fire share status changed event
        event(new ShareStatusChanged($share, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    //deposits
    public function create_deposit($id)
    {
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('share::deposit.create', compact('id', 'payment_types'));
    }

    public function store_deposit(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $share = Share::with('share_product')->find($id);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'share_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $share_transaction = new ShareTransaction();
        $share_transaction->created_by_id = Auth::id();
        $share_transaction->share_id = $share->id;
        $share_transaction->branch_id = $share->branch_id;
        $share_transaction->payment_detail_id = $payment_detail->id;
        $share_transaction->name = trans_choice('share::general.deposit', 1);
        $share_transaction->share_transaction_type_id = 1;
        $share_transaction->submitted_on = $request->date;
        $share_transaction->created_on = date("Y-m-d");
        $share_transaction->reversible = 1;
        $share_transaction->amount = $request->amount;
        $share_transaction->credit = $request->amount;
        $share_transaction->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Create Share Deposit');
        //fire transaction updated event
        event(new TransactionUpdated($share));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function edit_deposit($id)
    {
        $share_transaction = ShareTransaction::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('share::deposit.edit', compact('share_transaction', 'payment_types'));
    }

    public function update_deposit(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $share_transaction = ShareTransaction::find($id);
        $share = $share_transaction->share;
        //payment details
        $payment_detail = PaymentDetail::find($share_transaction->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        $share_transaction->submitted_on = $request->date;
        $share_transaction->amount = $request->amount;
        $share_transaction->credit = $request->amount;
        $share_transaction->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Update Share Deposit');
        //fire transaction updated event
        event(new TransactionUpdated($share));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    //withdrawals
    public function create_withdrawal($id)
    {
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('share::withdrawal.create', compact('id', 'payment_types'));
    }

    public function store_withdrawal(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $share = Share::with('share_product')->find($id);
        $balance = $share->transactions->where('reversed', 0)->sum('credit') - $share->transactions->where('reversed', 0)->sum('debit');
        if ($request->amount > $balance && $share->share_product->allow_overdraft == 0) {
            Flash::warning(trans_choice("share::general.insufficient_balance", 1));
            return redirect()->back()->withInput();
        }
        if ($request->amount > $balance && $share->share_product->allow_overdraft == 1 && $request->amount > $share->share_product->overdraft_limit) {
            Flash::warning(trans_choice("share::general.insufficient_overdraft_balance", 1));
            return redirect()->back()->withInput();
        }
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'share_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        $share_transaction = new ShareTransaction();
        $share_transaction->created_by_id = Auth::id();
        $share_transaction->share_id = $share->id;
        $share_transaction->branch_id = $share->branch_id;
        $share_transaction->payment_detail_id = $payment_detail->id;
        $share_transaction->name = trans_choice('share::general.withdrawal', 1);
        $share_transaction->share_transaction_type_id = 2;
        $share_transaction->submitted_on = $request->date;
        $share_transaction->created_on = date("Y-m-d");
        $share_transaction->reversible = 1;
        $share_transaction->amount = $request->amount;
        $share_transaction->debit = $request->amount;
        $share_transaction->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Create Share Withdrawal');
        //fire transaction updated event
        event(new TransactionUpdated($share));
        Flash::success(trans('core::general.successfully_saved'));
        return redirect('share/' . $share->id . '/show');
    }

    public function edit_withdrawal($id)
    {
        $share_transaction = ShareTransaction::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('share::withdrawal.edit', compact('share_transaction', 'payment_types'));
    }

    public function update_withdrawal(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $share_transaction = ShareTransaction::find($id);
        $share = $share_transaction->share;
        //payment details
        $payment_detail = PaymentDetail::find($share_transaction->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        $share_transaction->submitted_on = $request->date;
        $share_transaction->amount = $request->amount;
        $share_transaction->debit = $request->amount;
        $share_transaction->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Update Share Withdrawal');
        //fire transaction updated event
        event(new TransactionUpdated($share));
        Flash::success(trans('core::general.successfully_saved'));
        return redirect('share/' . $share->id . '/show');
    }

    //transactions
    public function show_transaction($id)
    {
        $share_transaction = ShareTransaction::with('payment_detail')->with('share')->find($id);
        return theme_view('share::share_transaction.show', compact('share_transaction'));
    }

    public function pdf_transaction($id)
    {
        $share_transaction = ShareTransaction::with('payment_detail')->with('share')->find($id);
        $pdf = PDF::loadView(theme_view_file('share::share_transaction.pdf'), compact('share_transaction'));
        return $pdf->download(trans_choice('share::general.transaction', 1) . ' ' . trans_choice('core::general.detail', 2) . ".pdf");
    }

    public function print_transaction($id)
    {
        $share_transaction = ShareTransaction::with('payment_detail')->with('share')->find($id);
        return theme_view('share::share_transaction.print', compact('share_transaction'));
    }

    public function edit_transaction($id)
    {
        $share_transaction = ShareTransaction::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('share::share_transaction.edit', compact('share_transaction', 'payment_types'));
    }

    public function update_transaction(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            // 'payment_type_id' => ['required'],
        ]);
        $share_transaction = ShareTransaction::find($id);
        $share_transaction_previous_amount = $share_transaction->amount;
        $share = $share_transaction->share;
        //payment details
        if ($share_transaction->payment_detail) {
            $payment_detail = PaymentDetail::find($share_transaction->payment_detail_id);
            $payment_detail->payment_type_id = $request->payment_type_id;
            $payment_detail->cheque_number = $request->cheque_number;
            $payment_detail->receipt = $request->receipt;
            $payment_detail->account_number = $request->account_number;
            $payment_detail->bank_name = $request->bank_name;
            $payment_detail->routing_code = $request->routing_code;
            $payment_detail->save();
        }
        $share_transaction->submitted_on = $request->date;
        $share_transaction->amount = $request->amount;
        if ($share_transaction->share_transaction_type_id == 1) {
            $share_transaction->credit = $request->amount;
        }
        if ($share_transaction->share_transaction_type_id == 2 || $share_transaction->share_transaction_type_id == 12) {
            $share_transaction->debit = $request->amount;
        }
        $share_transaction->credit = $request->amount;
        $share_transaction->save();
        if ($share_transaction->share_transaction_type_id == 12) {
            if (!empty($share_transaction->share_linked_charge)) {
                $share_transaction->share_linked_charge->paid_amount = $share_transaction->share_linked_charge->paid_amount - $share_transaction_previous_amount + $share_transaction->amount;
                if ($share_transaction->share_linked_charge->amount <= $share_transaction->share_linked_charge->paid_amount) {
                    $share_transaction->share_linked_charge->is_paid = 1;
                } else {
                    $share_transaction->share_linked_charge->is_paid = 0;
                }
                $share_transaction->share_linked_charge->save();
            }
        }
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Update Share Transaction');
        //fire transaction updated event
        Flash::success(trans('core::general.successfully_saved'));
        return redirect('share/' . $share->id . '/show');
    }

    public function create_share_linked_charge($id)
    {
        $share = Share::with('share_product')->with('share_product.charges')->with('share_product.charges.charge')->find($id);
        $charges = [];
        foreach ($share->share_product->charges as $key) {
            if ($key->charge->share_charge_type_id == 2) {
                $charges[$key->charge->id] = $key->charge;
            }
        }
        \JavaScript::put([
            'charges' => $charges
        ]);
        return theme_view('share::share_linked_charge.create', compact('share', 'charges'));
    }

    public function store_share_linked_charge(Request $request, $id)
    {
        $share = Share::with('share_product')->find($id);
        $request->validate([
            'amount' => ['required'],
            'share_charge_id' => ['required'],
            'date' => ['required', 'date'],
        ]);
        $share_charge = ShareCharge::find($request->share_charge_id);
        $share_linked_charge = new ShareLinkedCharge();
        $share_linked_charge->share_id = $share->id;
        $share_linked_charge->name = $share_charge->name;
        $share_linked_charge->share_charge_id = $share_charge->id;
        if ($share_charge->allow_override == 1) {
            $share_linked_charge->amount = $request->amount;
        } else {
            $share_linked_charge->amount = $share_charge->amount;
        }
        $share_linked_charge->calculated_amount = $share_charge->amount;
        $share_linked_charge->share_charge_type_id = $share_charge->share_charge_type_id;
        $share_linked_charge->share_charge_option_id = $share_charge->share_charge_option_id;
        $share_linked_charge->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Create Share Charge');
        Flash::success(trans('core::general.successfully_saved'));
        return redirect('share/' . $id . '/show');
    }

    public function pay_charge($id)
    {
        $share_linked_charge = ShareLinkedCharge::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('share::share_linked_charge.pay', compact('share_linked_charge', 'payment_types'));
    }

    public function store_pay_charge(Request $request, $id)
    {
        $share_linked_charge = ShareLinkedCharge::with('share')->find($id);
        $share = $share_linked_charge->share;
        $request->validate([
            'amount' => ['required', 'max:' . ($share_linked_charge->amount - $share_linked_charge->paid_amount)],
            'payment_type_id' => ['required'],
            'date' => ['required', 'date'],
        ]);

        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'share_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        $share_transaction = new ShareTransaction();
        $share_transaction->created_by_id = Auth::id();
        $share_transaction->share_id = $share->id;
        $share_transaction->branch_id = $share->branch_id;
        $share_transaction->payment_detail_id = $payment_detail->id;
        $share_transaction->name = trans_choice('share::general.pay', 1) . ' ' . trans_choice('share::general.charge', 1);
        $share_transaction->share_transaction_type_id = 12;
        $share_transaction->submitted_on = $request->date;
        $share_transaction->created_on = date("Y-m-d");
        $share_transaction->reversible = 1;
        $share_transaction->amount = $request->amount;
        $share_transaction->debit = $request->amount;
        $share_transaction->share_linked_charge_id = $id;
        $share_transaction->save();
        $share_linked_charge->paid_amount = $share_linked_charge->paid_amount + $request->amount;
        if ($share_linked_charge->amount <= $share_linked_charge->paid_amount) {
            $share_linked_charge->is_paid = 1;
        }
        $share_linked_charge->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Pay Share Charge');
        event(new TransactionUpdated($share));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }

    public function reverse_transaction(Request $request, $id)
    {

        $share_transaction = ShareTransaction::find($id);
        $share = $share_transaction->share;
        if ($share_transaction->share_transaction_type_id == 12) {
            if (!empty($share_transaction->share_linked_charge)) {
                $share_transaction->share_linked_charge->paid_amount = $share_transaction->share_linked_charge->paid_amount - $share_transaction->amount;
                if ($share_transaction->share_linked_charge->amount <= $share_transaction->share_linked_charge->paid_amount) {
                    $share_transaction->share_linked_charge->is_paid = 1;
                } else {
                    $share_transaction->share_linked_charge->is_paid = 0;
                }
                $share_transaction->share_linked_charge->save();
            }
        }
        if ($share_transaction->debit > $share_transaction->credit) {
            $share_transaction->credit = $share_transaction->debit;
        } else {
            $share_transaction->debit = $share_transaction->credit;
        }
        $share_transaction->reversed = 1;
        $share_transaction->reversible = 0;
        $share_transaction->save();
        activity()->on($share)
            ->withProperties(['id' => $share->id])
            ->log('Reverse Share Transaction');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/' . $share->id . '/show');
    }
}
