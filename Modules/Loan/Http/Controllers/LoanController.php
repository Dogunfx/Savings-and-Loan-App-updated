<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\CustomField\Entities\CustomField;
use Modules\Loan\Entities\Fund;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanApplication;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanHistory;
use Modules\Loan\Entities\LoanLinkedCharge;
use Modules\Loan\Entities\LoanOfficerHistory;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanPurpose;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\LoanStatusChanged;
use Modules\Loan\Events\TransactionUpdated;
use Modules\User\Entities\User;
use JavaScript;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.index'])->only(['index', 'get_loans', 'show', 'show_application', 'get_applications']);
        $this->middleware(['permission:loan.loans.create'])->only(['create', 'create_client_loan', 'store_client_loan', 'store']);
        $this->middleware(['permission:loan.loans.edit'])->only(['edit', 'edit_client_loan', 'update', 'update_client_loan', 'change_loan_officer']);
        $this->middleware(['permission:loan.loans.destroy'])->only(['destroy']);
        $this->middleware(['permission:loan.loans.approve_loan'])->only(['approve_loan', 'undo_approval', 'reject_loan', 'undo_rejection', 'approve_application', 'store_approve_application']);
        $this->middleware(['permission:loan.loans.disburse_loan'])->only(['disburse_loan', 'undo_disbursement']);
        $this->middleware(['permission:loan.loans.withdraw_loan'])->only(['withdraw_loan', 'undo_withdrawn']);
        $this->middleware(['permission:loan.loans.write_off_loan'])->only(['write_off_loan', 'undo_write_off']);
        $this->middleware(['permission:loan.loans.reschedule_loan'])->only(['reschedule_loan']);
        $this->middleware(['permission:loan.loans.close_loan'])->only(['close_loan', 'undo_close']);
        $this->middleware(['permission:loan.loans.calculator'])->only(['calculator']);
        $this->middleware(['permission:loan.loans.transactions.create'])->only(['create_repayment', 'store_repayment', 'create_loan_linked_charge', 'store_loan_linked_charge']);
        $this->middleware(['permission:loan.loans.transactions.edit'])->only(['edit_repayment', 'reverse_repayment', 'update_repayment', 'waive_interest', 'waive_charge']);

    }

    private function isAdmin() {
        return Auth::user()->hasRole('admin');
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
        $loan_officer_id = $request->loan_officer_id;
        $branch_id = $request->branch_id;
        $data = Loan::leftJoin("clients", "clients.id", "loans.client_id")
            ->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")
            ->leftJoin("branches", "branches.id", "loans.branch_id")
            ->leftJoin("users", "users.id", "loans.loan_officer_id")
            ->when($client_id, function ($query) use ($client_id) {
                $query->where("loans.client_id", $client_id);
            })
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loans.branch_id", $branch_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('loan_products.name', 'like', "%$search%");
                $query->orWhere('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
                $query->orWhere('loans.id', 'like', "%$search%");
                $query->orWhere('loans.account_number', 'like', "%$search%");
                $query->orWhere('loans.external_id', 'like', "%$search%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where("loans.status", $status);
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when(!$this->isAdmin(), function ($query) use ($status) {
                $query->where("loans.loan_officer_id", Auth::id());
            })
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.applied_amount,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")
            ->groupBy("loans.id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::loan.index', compact('data'));
    }

    public function application(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $status = $request->status;
        $client_id = $request->client_id;
        $loan_officer_id = $request->loan_officer_id;
        $branch_id = $request->branch_id;
        $data = LoanApplication::leftJoin("clients", "clients.id", "loan_applications.client_id")
            ->leftJoin("loan_products", "loan_products.id", "loan_applications.loan_product_id")
            ->leftJoin("branches", "branches.id", "loan_applications.branch_id")
            ->leftJoin("users", "users.id", "loan_applications.created_by_id")
            ->when($status, function ($query) use ($status) {
                $query->where("loan_applications.status", $status);
            })->when($client_id, function ($query) use ($client_id) {
                $query->where("loan_applications.client_id", $client_id);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loan_applications.branch_id", $branch_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('loan_products.name', 'like', "%$search%");
                $query->orWhere('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
                $query->orWhere('loan_applications.id', 'like', "%$search%");
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when(!$this->isAdmin(), function ($query) use ($status) {
                $query->where("loans.loan_officer_id", Auth::id());
            })
            
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) created_by,loan_applications.id,loan_applications.client_id,loan_products.name loan_product,loan_applications.status,loan_applications.loan_id,branches.name branch,loan_applications.amount,loan_applications.created_at")
            ->groupBy("loan_applications.id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::application.index',compact('data'));
    }

    public function get_loans(Request $request)
    {

        $status = $request->status;
        $client_id = $request->client_id;
        $loan_officer_id = $request->loan_officer_id;
        $branch_id = $request->branch_id;
        $query = DB::table("loans")
            ->leftJoin("clients", "clients.id", "loans.client_id")
            ->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")
            ->leftJoin("branches", "branches.id", "loans.branch_id")
            ->leftJoin("users", "users.id", "loans.loan_officer_id")
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.applied_amount,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")->when($status, function ($query) use ($status) {
                $query->where("loans.status", $status);
            })->when($client_id, function ($query) use ($client_id) {
                $query->where("loans.client_id", $client_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loans.branch_id", $branch_id);
            })->groupBy("loans.id");
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
        })->editColumn('principal', function ($data) {
            return number_format($data->principal, $data->decimals);
        })->editColumn('total_principal', function ($data) {
            return number_format($data->total_principal, $data->decimals);
        })->editColumn('total_interest', function ($data) {
            return number_format($data->total_interest, $data->decimals);
        })->editColumn('total_fees', function ($data) {
            return number_format($data->total_fees, $data->decimals);
        })->editColumn('total_penalties', function ($data) {
            return number_format($data->total_penalties, $data->decimals);
        })->editColumn('due', function ($data) {
            return number_format($data->total_principal + $data->total_interest + $data->total_fees + $data->total_penalties, $data->decimals);
        })->editColumn('balance', function ($data) {
            return number_format(($data->total_principal - $data->principal_repaid_derived - $data->principal_written_off_derived) + ($data->total_interest - $data->interest_repaid_derived - $data->interest_written_off_derived - $data->interest_waived_derived) + ($data->total_fees - $data->fees_repaid_derived - $data->fees_written_off_derived - $data->fees_waived_derived) + ($data->total_penalties - $data->penalties_repaid_derived - $data->penalties_written_off_derived - $data->penalties_waived_derived), $data->decimals);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'submitted') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending_approval', 1) . '</span>';
            }
            if ($data->status == 'overpaid') {
                return '<span class="label label-warning">' . trans_choice('loan::general.overpaid', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('loan::general.awaiting_disbursement', 1) . '</span>';
            }
            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('loan::general.active', 1) . '</span>';
            }
            if ($data->status == 'rejected') {
                return '<span class="label label-danger">' . trans_choice('loan::general.rejected', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('loan::general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'written_off') {
                return '<span class="label label-danger">' . trans_choice('loan::general.written_off', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('loan::general.closed', 1) . '</span>';
            }
            if ($data->status == 'pending_reschedule') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending_reschedule', 1) . '</span>';
            }
            if ($data->status == 'rescheduled') {
                return '<span class="label label-info">' . trans_choice('loan::general.rescheduled', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('loan/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'client', 'action', 'status'])->make(true);
    }

    public function get_applications(Request $request)
    {

        $status = $request->status;
        $client_id = $request->client_id;
        $branch_id = $request->branch_id;

        $query = DB::table("loan_applications")
            ->leftJoin("clients", "clients.id", "loan_applications.client_id")
            ->leftJoin("loan_products", "loan_products.id", "loan_applications.loan_product_id")
            ->leftJoin("branches", "branches.id", "loan_applications.branch_id")
            ->leftJoin("users", "users.id", "loan_applications.created_by_id")
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) created_by,loan_applications.id,loan_applications.client_id,loan_products.name loan_product,loan_applications.status,loan_applications.loan_id,branches.name branch,loan_applications.amount,loan_applications.created_at")
            ->when($status, function ($query) use ($status) {
            $query->where("loan_applications.status", $status);
        })->when($client_id, function ($query) use ($client_id) {
            $query->where("loan_applications.client_id", $client_id);
        })->when($branch_id, function ($query) use ($branch_id) {
            $query->where("loan_applications.branch_id", $branch_id);
        });
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
        })->editColumn('amount', function ($data) {
            return number_format($data->amount, 2);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'submitted') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending_approval', 1) . '</span>';
            }
            if ($data->status == 'overpaid') {
                return '<span class="label label-warning">' . trans_choice('loan::general.overpaid', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-success">' . trans_choice('loan::general.approved', 1) . '</span>';
            }
            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('loan::general.active', 1) . '</span>';
            }
            if ($data->status == 'rejected') {
                return '<span class="label label-danger">' . trans_choice('loan::general.rejected', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('loan::general.withdrawn', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('loan/application/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/application/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'client', 'action', 'status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->get();
        $loan_products = LoanProduct::with('charges')->with('charges.charge')->where('active', 1)->get();
        $funds = Fund::all();
        $loan_purposes = LoanPurpose::get();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $custom_fields = CustomField::where('category', 'add_loan')->where('active', 1)->get();

        JavaScript::put([
            'clients' => $clients,
            'loan_products' => $loan_products,
            'loan_charges' => LoanCharge::get(),
            'funds' => $funds,
            'loan_purposes' => $loan_purposes,
            'users' => $users

        ]);
        return theme_view('loan::loan.create', compact('clients', 'loan_products', 'funds', 'loan_purposes', 'users', 'custom_fields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'client_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'interest_rate' => ['required', 'numeric'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $loan_product = LoanProduct::find($request->loan_product_id);
        $client = Client::find($request->client_id);
        $loan = new Loan();
        $loan->currency_id = $loan_product->currency_id;
        $loan->loan_product_id = $loan_product->id;
        $loan->client_id = $client->id;
        $loan->branch_id = $client->branch_id;
        $loan->loan_transaction_processing_strategy_id = $loan_product->loan_transaction_processing_strategy_id;
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->created_by_id = Auth::id();
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_rate_type = $loan_product->interest_rate_type;
        $loan->grace_on_principal_paid = $loan_product->grace_on_principal_paid;
        $loan->grace_on_interest_paid = $loan_product->grace_on_interest_paid;
        $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
        $loan->interest_methodology = $loan_product->interest_methodology;
        $loan->amortization_method = $loan_product->amortization_method;
        $loan->auto_disburse = $loan_product->auto_disburse;
        $loan->submitted_on_date = date("Y-m-d");
        $loan->submitted_by_user_id = Auth::id();
        $loan->save();
        //save charges
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $loan_charge = LoanCharge::find($key);
                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $key;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $value;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
            }
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Created';
        $loan_history->save();
        $loan_officer_history = new LoanOfficerHistory();
        $loan_officer_history->loan_id = $loan->id;
        $loan_officer_history->created_by_id = Auth::id();
        $loan_officer_history->loan_officer_id = $request->loan_officer_id;
        $loan_officer_history->start_date = date("Y-m-d");
        $loan_officer_history->save();
        custom_fields_save_form('add_loan', $request, $loan->id);
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Create Loan');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        //fire loan status changed event
        event(new LoanStatusChanged($loan));
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function reschedule_loan(Request $request, $id)
    {
        $request->validate([
            'rescheduled_from_date' => ['required', 'date'],
            'rescheduled_on_date' => ['required', 'date'],
            'rescheduled_first_payment_date' => ['required_if:reschedule_first_payment_date,on', 'date'],
            'reschedule_grace_on_principal_paid' => ['nullable', 'numeric'],
            'reschedule_grace_on_interest_paid' => ['nullable', 'numeric'],
            'reschedule_extra_installments' => ['required_if:reschedule_add_extra_installments,on', 'numeric'],
            'reschedule_interest_rate' => ['required_if:reschedule_adjust_loan_interest_rate,on', 'numeric'],
        ]);
        $loan = Loan::find($id);

        if (empty($loan->repayment_schedules->where('due_date', $request->rescheduled_from_date)->first())) {
            \flash(trans_choice("loan::general.no_installment_schedule_found", 1))->warning()->important();
            return redirect()->back();
        }
        $reschedule_principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->where('due_date', '<', $request->rescheduled_from_date)->sum('principal');
        LoanRepaymentSchedule::where('due_date', '>=', $request->rescheduled_from_date)->where('loan_id', $loan->id)->delete();
        $loan_product = $loan->loan_product;
        $client = $loan->client;
        $interest_rate = determine_period_interest_rate($request->reschedule_interest_rate ?: $loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type);
        $balance = round($reschedule_principal, $loan->decimals);
        $period = $loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date)->count() + $request->reschedule_extra_installments;
        $payment_from_date = $request->rescheduled_on_date;
        $next_payment_date = $request->rescheduled_first_payment_date ?: $loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date)->first()->due_date;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to use
            //flat  method
            if ($loan->interest_methodology == 'flat') {
                $principal = round($reschedule_principal / $period, $loan->decimals);
                $interest = round($interest_rate * $principal, $loan->decimals);
                if ($request->reschedule_grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $reschedule_principal, $period), $loan->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan->decimals);
                    $principal = round(($amortized_payment - $interest), $loan->decimals);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = round($reschedule_principal / $period, $loan->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan->decimals);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }

            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }

        $loan->load('repayment_schedules');
        $total_principal = $loan->repayment_schedules->sum('principal');
        $total_interest = $loan->repayment_schedules->sum('interest');
        foreach ($loan->charges->whereIn('loan_charge_type_id', [3, 2]) as $key) {
            //installment_fee
            $total_calculated_amount = 0;
            if ($key->loan_charge_type_id == 3) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), $loan->decimals);
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), $loan->decimals);
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), $loan->decimals);
                }

                //reverse and create new transaction
                if (!empty($key->transaction)) {
                    $key->transaction->credit = $key->transaction->amount;
                    $key->transaction->debit = $key->transaction->amount;
                    $key->transaction->reversed = 1;
                    $key->transaction->save();
                }

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->branch_id = $loan->branch_id;
                $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
                $loan_transaction->loan_transaction_type_id = 10;
                $loan_transaction->submitted_on = $loan->disbursed_on_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $key->calculated_amount;
                $loan_transaction->debit = $key->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $key->loan_transaction_id = $loan_transaction->id;
                $key->save();
                foreach ($loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date) as $loan_repayment_schedule) {
                    if ($key->loan_charge_option_id == 2) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), $loan->decimals);
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), $loan->decimals);
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), $loan->decimals);
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                }
            }
        }

        $loan->save();

        $loan->expected_maturity_date = $next_payment_date;
        $loan->rescheduled_on_date = $request->rescheduled_on_date;
        $loan->rescheduled_notes = $request->rescheduled_notes;
        $loan->rescheduled_by_user_id = Auth::id();
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Rescheduled';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reschedule Loan');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show_application($id)
    {
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $payment_types = PaymentType::where('active', 1)->get();
        $loan_application = LoanApplication::with('client')->with('loan_product')->find($id);
        return theme_view('loan::application.show', compact('loan_application', 'users', 'payment_types'));
    }

    public function show($id)
    {
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $payment_types = PaymentType::where('active', 1)->get();
        $loan = Loan::with('repayment_schedules')->with('transactions')->with('charges')->with('client')->with('loan_product')->with('notes')->with('guarantors')->with('files')->with('collateral')->with('collateral.collateral_type')->with('notes.created_by')->find($id);
        $custom_fields = CustomField::where('category', 'add_loan')->where('active', 1)->get();
        return theme_view('loan::loan.show', compact('loan', 'users', 'payment_types', 'custom_fields'));
    }


    public function edit($id)
    {

        $loan = Loan::with('charges')->with('client')->with('loan_product')->find($id);
        $client = $loan->client;
        $loan_product = $loan->loan_product;
        $funds = Fund::all();
        $loan_purposes = LoanPurpose::get();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $charges = [];
        $charges_list = [];
        $temp_charges = [];
        foreach ($loan->charges as $key) {
            $temp_charges[] = $key->loan_charge_id;
        }
        foreach ($loan_product->charges as $key) {
            if (!empty($key->charge)) {
                //charge type
                if ($key->charge->loan_charge_type_id == 1) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement', 1);
                }
                if ($key->charge->loan_charge_type_id == 2) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.specified_due_date', 1);
                }
                if ($key->charge->loan_charge_type_id == 3) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 4) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 5) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement_paid_with_repayment', 1);
                }
                if ($key->charge->loan_charge_type_id == 6) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.loan_rescheduling_fee', 1);
                }
                if ($key->charge->loan_charge_type_id == 7) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue_on_loan_maturity', 1);
                }
                if ($key->charge->loan_charge_type_id == 8) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.last_installment_fee', 1);
                }
                //charge option
                if ($key->charge->loan_charge_option_id == 1) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.flat', 1);
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.total_outstanding_loan_principal', 1);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.original_loan_principal', 1);
                }
                $charges[$key->charge->id] = $key;
                if (in_array($key->charge->id, $temp_charges)) {
                    $charges_list[] = $key;
                }
            }
        }
        JavaScript::put([
            'loan_product' => $loan_product,
            'charges' => $charges,
            'original_charges' => $charges,
            'charges_list' => $charges_list,
            'funds' => $funds,
            'loan_purposes' => $loan_purposes,

        ]);
        $custom_fields = CustomField::where('category', 'add_loan')->where('active', 1)->get();
        return theme_view('loan::loan.edit', compact('client', 'loan_product', 'users', 'loan_purposes', 'funds', 'loan', 'custom_fields'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'client_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'interest_rate' => ['required', 'numeric'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $loan_product = LoanProduct::find($request->loan_product_id);
        $loan = Loan::find($id);
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_rate_type = $loan_product->interest_rate_type;
        $loan->save();
        //save charges
        LoanLinkedCharge::where('loan_id', $id)->delete();
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $loan_charge = LoanCharge::find($key);
                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $key;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $value;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
            }
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Update Loan');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function approve_loan(Request $request, $id)
    {

        $request->validate([
            'approved_on_date' => ['required', 'date'],
            'approved_amount' => ['required', 'numeric'],
        ]);
        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = Auth::id();
        $loan->approved_amount = $request->approved_amount;
        $loan->approved_on_date = $request->approved_on_date;
        $loan->status = 'approved';
        $loan->approved_notes = $request->approved_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Approved';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Approve Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_approval(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = null;
        $loan->approved_amount = null;
        $loan->approved_on_date = null;
        $loan->status = 'submitted';
        $loan->approved_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unapproved';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Approval');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function reject_loan(Request $request, $id)
    {

        $request->validate([
            'rejected_notes' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->rejected_by_user_id = Auth::id();
        $loan->rejected_on_date = date("Y-m-d");
        $loan->status = 'rejected';
        $loan->rejected_notes = $request->rejected_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Rejected';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reject Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_rejection(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->rejected_by_user_id = null;
        $loan->rejected_on_date = null;
        $loan->status = 'submitted';
        $loan->rejected_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unrejected';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Rejection');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function withdraw_loan(Request $request, $id)
    {

        $request->validate([
            'withdrawn_notes' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->withdrawn_by_user_id = Auth::id();
        $loan->withdrawn_on_date = date("Y-m-d");
        $loan->status = 'withdrawn';
        $loan->withdrawn_notes = $request->withdrawn_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Withdrawn';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Withdraw Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_withdrawn(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->withdrawn_by_user_id = null;
        $loan->withdrawn_on_date = null;
        $loan->status = 'submitted';
        $loan->withdrawn_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unwithdrawn';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Withdrawal');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function write_off_loan(Request $request, $id)
    {

        $request->validate([
            'written_off_on_date' => ['required'],
            'written_off_notes' => ['required'],
        ]);
        $loan = Loan::with('repayment_schedules')->find($id);
        $principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_written_off_derived') - $loan->repayment_schedules->sum('principal_repaid_derived');
        $interest = $loan->repayment_schedules->sum('interest') - $loan->repayment_schedules->sum('interest_written_off_derived') - $loan->repayment_schedules->sum('interest_repaid_derived') - $loan->repayment_schedules->sum('interest_waived_derived');
        $fees = $loan->repayment_schedules->sum('fees') - $loan->repayment_schedules->sum('fees_written_off_derived') - $loan->repayment_schedules->sum('fees_repaid_derived') - $loan->repayment_schedules->sum('fees_waived_derived');
        $penalties = $loan->repayment_schedules->sum('penalties') - $loan->repayment_schedules->sum('penalties_written_off_derived') - $loan->repayment_schedules->sum('penalties_repaid_derived') - $loan->repayment_schedules->sum('penalties_waived_derived');
        $balance = $principal + $interest + $fees + $penalties;
        $previous_status = $loan->status;
        $loan->written_off_by_user_id = Auth::id();
        $loan->written_off_on_date = date("Y-m-d");
        $loan->status = 'written_off';
        $loan->written_off_notes = $request->written_off_notes;
        $loan->save();
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.write_off', 1);
        $loan_transaction->loan_transaction_type_id = 6;
        $loan_transaction->submitted_on = $loan->written_off_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $balance;
        $loan_transaction->credit = $balance;
        $loan_transaction->save();
        //check if accounting is enabled
        if ($loan->loan_product->accounting_rule == "cash" || $loan->loan_product->accounting_rule == "accrual_periodic" || $loan->loan_product->accounting_rule == "accrual_upfront") {
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $loan_transaction->id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->loan_portfolio_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_write_off';
            $journal_entry->date = $loan->written_off_on_date;
            $date = explode('-', $loan->written_off_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $balance;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $loan_transaction->id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->losses_written_off_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_write_off';
            $journal_entry->date = $loan->written_off_on_date;
            $date = explode('-', $loan->written_off_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $balance;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();

        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Written off';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Writeoff Loan');
        event(new TransactionUpdated($loan));
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_write_off(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->written_off_by_user_id = null;
        $loan->written_off_on_date = null;
        $loan->status = 'active';
        $loan->written_off_notes = null;
        $loan->save();
        foreach (LoanTransaction::where('loan_id', $loan->id)->where('loan_transaction_type_id', 6)->where('reversed', 0)->get() as $key) {
            $key->amount = 0;
            $key->debit = $key->credit;
            $key->reversed = 1;
            $key->save();
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unwritten off';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan writeoff');
        event(new TransactionUpdated($loan));
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function change_loan_officer(Request $request, $id)
    {

        $request->validate([
            'loan_officer_id' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_loan_officer_id = $loan->loan_officer_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->save();
        if ($previous_loan_officer_id != $request->loan_officer_id) {
            $previous_loan_officer = LoanOfficerHistory::where('loan_id', $loan->id)->where('loan_officer_id', $request->loan_officer_id)->where('end_date', '')->first();
            if (!empty($previous_loan_officer)) {
                $previous_loan_officer->end_date = date("Y-m-d");
                $previous_loan_officer->save();
            }
            $loan_officer_history = new LoanOfficerHistory();
            $loan_officer_history->loan_id = $loan->id;
            $loan_officer_history->created_by_id = Auth::id();
            $loan_officer_history->loan_officer_id = $request->loan_officer_id;
            $loan_officer_history->start_date = date("Y-m-d");
            $loan_officer_history->save();
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Change Loan Officer');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function disburse_loan(Request $request, $id)
    {


        $request->validate([
            'disbursed_on_date' => ['required', 'date'],
            'first_payment_date' => ['required', 'date', 'after:disbursed_on_date'],
            'payment_type_id' => ['required'],
        ]);

        $loan = Loan::find($id);
        if ($loan->status != 'approved') {
            \flash(trans_choice('loan::general.loan', 1) . ' ' . trans_choice('core::general.not', 1) . ' ' . trans_choice('loan::general.approved', 1))->warning()->important();
            return redirect()->back();
        }
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
        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = Auth::id();
        $loan->disbursed_on_date = $request->disbursed_on_date;
        $loan->first_payment_date = $request->first_payment_date;
        $loan->principal = $loan->approved_amount;
        $loan->status = 'active';

        //prepare loan schedule
        //determine interest rate
        $interest_rate = determine_period_interest_rate($loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type,$loan->repayment_frequency);
        $balance = round($loan->principal, $loan->decimals);
        $period = ($loan->loan_term / $loan->repayment_frequency);
        $payment_from_date = $request->disbursed_on_date;
        $next_payment_date = $request->first_payment_date;
        $total_principal = 0;
        $total_interest = 0;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to use
            //flat  method
            if ($loan->interest_methodology == 'flat') {
                $principal = round($loan->principal / $period, $loan->decimals);
                $interest = round($interest_rate * $loan->principal, $loan->decimals);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $loan->principal, $period), $loan->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan->decimals);
                    $principal = round(($amortized_payment - $interest), $loan->decimals);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = round($loan->principal / $period, $loan->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan->decimals);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, $loan->decimals);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }

            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            if($loan->repayment_frequency_type=='months'){
                $next_payment_date = Carbon::parse($next_payment_date)->addMonthsNoOverflow($loan->repayment_frequency)->format("Y-m-d");
            }else{
                $next_payment_date = Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            }
            $total_principal = $total_principal + $loan_repayment_schedule->principal;
            $total_interest = $total_interest + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }
        $loan->expected_maturity_date = $next_payment_date;
        $loan->principal_disbursed_derived = $total_principal;
        $loan->interest_disbursed_derived = $total_interest;

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Disbursed';
        $loan_history->save();
        //add disbursal transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->branch_id = $loan->branch_id;
        $loan_transaction->payment_detail_id = $payment_detail->id;
        $loan_transaction->name = trans_choice('loan::general.disbursement', 1);
        $loan_transaction->loan_transaction_type_id = 1;
        $loan_transaction->submitted_on = $loan->disbursed_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $loan->principal;
        $loan_transaction->debit = $loan->principal;
        $disbursal_transaction_id = $loan_transaction->id;
        $loan_transaction->save();
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->branch_id = $loan->branch_id;
        $loan_transaction->name = trans_choice('loan::general.interest', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
        $loan_transaction->loan_transaction_type_id = 11;
        $loan_transaction->submitted_on = $loan->disbursed_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $total_interest;
        $loan_transaction->debit = $total_interest;
        $loan_transaction->save();
        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($loan->charges as $key) {
            //disbursement
            if ($key->loan_charge_type_id == 1) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), $loan->decimals);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), $loan->decimals);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), $loan->decimals);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
            }
            //installment_fee
            if ($key->loan_charge_type_id == 3) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), $loan->decimals);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), $loan->decimals);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), $loan->decimals);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), $loan->decimals);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                //create transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->branch_id = $loan->branch_id;
                $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
                $loan_transaction->loan_transaction_type_id = 10;
                $loan_transaction->submitted_on = $loan->disbursed_on_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $key->calculated_amount;
                $loan_transaction->debit = $key->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $key->loan_transaction_id = $loan_transaction->id;
                $key->save();
                //add the charges to the schedule
                foreach ($loan->repayment_schedules as $loan_repayment_schedule) {
                    if ($key->loan_charge_option_id == 2) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), $loan->decimals);
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), $loan->decimals);
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), $loan->decimals);
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                }
            }

        }
        if ($disbursement_fees > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Auth::id();
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->name = trans_choice('loan::general.disbursement', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.charge', 2);
            $loan_transaction->loan_transaction_type_id = 5;
            $loan_transaction->submitted_on = $loan->disbursed_on_date;
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $disbursement_fees;
            $loan_transaction->credit = $disbursement_fees;
            $loan_transaction->fees_repaid_derived = $disbursement_fees;
            $loan_transaction->save();
            $disbursement_fees_transaction_id = $loan_transaction->id;
        }
        $loan->disbursement_charges = $disbursement_fees;
        $loan->save();
        //check if accounting is enabled
        if ($loan->loan_product->accounting_rule == "cash" || $loan->loan_product->accounting_rule == "accrual_periodic" || $loan->loan_product->accounting_rule == "accrual_upfront") {
            //loan disbursal
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->fund_source_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_disbursement';
            $journal_entry->date = $loan->disbursed_on_date;
            $date = explode('-', $loan->disbursed_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $loan->principal;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->loan_portfolio_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_disbursement';
            $journal_entry->date = $loan->disbursed_on_date;
            $date = explode('-', $loan->disbursed_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $loan->principal;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //
            if ($disbursement_fees > 0) {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->payment_detail_id = $payment_detail->id;
                $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->loan_product->income_from_fees_chart_of_account_id;
                $journal_entry->transaction_type = 'repayment_at_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                $journal_entry->payment_detail_id = $payment_detail->id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->loan_product->fund_source_chart_of_account_id;
                $journal_entry->transaction_type = 'repayment_at_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
            }
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Disburse Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_disbursement(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = null;
        $loan->disbursed_on_date = null;
        $loan->status = 'approved';
        $loan->disbursed_notes = null;
        $loan->save();
        //destroy loan repayment schedules
        LoanLinkedCharge::where('loan_id', $loan->id)->update(["loan_transaction_id" => null]);
        LoanRepaymentSchedule::where('loan_id', $loan->id)->delete();
        LoanTransaction::where('loan_id', $loan->id)->delete();
        //reverse journal entries
        JournalEntry::whereIn('transaction_type', ['repayment_at_disbursement', 'loan_disbursement', 'loan_repayment'])->where('reference', $loan->id)->update(["reversed" => 1]);
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Undisbursed';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Disbursement');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    //transactions
    public function show_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        return theme_view('loan::loan_transaction.show', compact('loan_transaction'));
    }

    public function pdf_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        $pdf = PDF::loadView(theme_view_file('loan::loan_transaction.pdf'), compact('loan_transaction'));
        return $pdf->download(trans_choice('loan::general.transaction', 1) . ' ' . trans_choice('loan::general.detail', 2) . ".pdf");
    }

    public function print_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        return theme_view('loan::loan_transaction.print', compact('loan_transaction'));
    }

    //schedules
    public function email_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        //return theme_view('loan::loan_schedule.email', compact('loan'));
    }

    public function pdf_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        $pdf = PDF::loadView(theme_view_file('loan::loan_schedule.pdf'), compact('loan'))->setPaper('a4', 'landscape');
        return $pdf->download(trans_choice('loan::general.repayment', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf");
    }

    public function print_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        return theme_view('loan::loan_schedule.print', compact('loan'));
    }

    //repayments
    public function create_repayment($id)
    {
        $payment_types = PaymentType::where('active', 1)->get();
        $custom_fields = CustomField::where('category', 'add_repayment')->where('active', 1)->get();
        return theme_view('loan::loan_repayment.create', compact('id', 'payment_types', 'custom_fields'));
    }

    public function store_repayment(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $loan = Loan::with('loan_product')->find($id);
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
        $payment_detail->description = $request->description;
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
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Create Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function edit_repayment($id)
    {
        $loan_transaction = LoanTransaction::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        $custom_fields = CustomField::where('category', 'add_repayment')->where('active', 1)->get();
        return theme_view('loan::loan_repayment.edit', compact('loan_transaction', 'payment_types', 'custom_fields'));
    }

    public function update_repayment(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $loan_transaction = LoanTransaction::find($id);
        $loan = $loan_transaction->loan;
        //payment details
        $payment_detail = PaymentDetail::find($loan_transaction->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->amount = $request->amount;
        $loan_transaction->credit = $request->amount;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Update Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function waive_charge(Request $request, $id)
    {

        $loan_linked_charge = LoanLinkedCharge::with('loan')->with('transaction')->find($id);
        $loan_linked_charge->waived = 1;
        $loan_linked_charge->save();
        $loan = $loan_linked_charge->loan;
        $loan_transaction = $loan_linked_charge->transaction;
        $loan_transaction->credit = $loan_transaction->amount;
        $loan_transaction->debit = $loan_transaction->amount;
        $loan_transaction->reversed = 1;
        $loan_transaction->save();
        if ($loan_linked_charge->loan_charge_type_id == 2 || $loan_linked_charge->loan_charge_type_id == 4 || $loan_linked_charge->loan_charge_type_id == 6 || $loan_linked_charge->loan_charge_type_id == 2 || $loan_linked_charge->loan_charge_type_id == 7 || $loan_linked_charge->loan_charge_type_id == 8) {
            $repayment_schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', $loan_transaction->due_date)->first();
            if ($loan_linked_charge->is_penalty == 1) {
                $repayment_schedule->penalties_waived_derived = $repayment_schedule->penalties_waived_derived + $loan_linked_charge->calculated_amount;
            } else {
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $loan_linked_charge->calculated_amount;
            }
            $repayment_schedule->save();
        }
        if ($loan_linked_charge->loan_charge_type_id == 3) {
            $amount = 0;
            foreach ($loan->repayment_schedules as $repayment_schedule) {
                if ($loan_linked_charge->loan_charge_option_id == 1) {
                    $amount = $loan_linked_charge->calculated_amount;
                }
                if ($loan_linked_charge->loan_charge_option_id == 2) {
                    $amount = round(($loan_linked_charge->amount * $repayment_schedule->principal / 100), $loan->decimals);
                }
                if ($loan_linked_charge->loan_charge_option_id == 3) {
                    $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest + $repayment_schedule->principal) / 100), $loan->decimals);
                }
                if ($loan_linked_charge->loan_charge_option_id == 4) {
                    $amount = round(($loan_linked_charge->amount * $repayment_schedule->interest / 100), $loan->decimals);
                }
                if ($loan_linked_charge->loan_charge_option_id == 5) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
                }
                if ($loan_linked_charge->loan_charge_option_id == 6) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
                }
                if ($loan_linked_charge->loan_charge_option_id == 7) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
                }
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $amount;
                $repayment_schedule->save();
            }
        }
        activity()->on($loan_linked_charge)
            ->withProperties(['id' => $loan_linked_charge->id])
            ->log('Waive Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function create_loan_linked_charge($id)
    {
        $loan = Loan::with('loan_product')->with('loan_product.charges')->with('loan_product.charges.charge')->find($id);
        $charges = [];
        foreach ($loan->loan_product->charges as $key) {
            if ($key->charge->loan_charge_type_id == 2) {
                $charges[$key->charge->id] = $key->charge;
            }
        }
        JavaScript::put([
            'charges' => $charges
        ]);
        return theme_view('loan::loan_linked_charge.create', compact('loan', 'charges'));
    }

    public function store_loan_linked_charge(Request $request, $id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        $request->validate([
            'amount' => ['required'],
            'loan_charge_id' => ['required'],
            'date' => ['required', 'date'],
        ]);
        $loan_charge = LoanCharge::find($request->loan_charge_id);
        $loan_linked_charge = new LoanLinkedCharge();
        $loan_linked_charge->loan_id = $loan->id;
        $loan_linked_charge->name = $loan_charge->name;
        $loan_linked_charge->loan_charge_id = $loan_charge->id;
        if ($loan_charge->allow_override == 1) {
            $loan_linked_charge->amount = $request->amount;
        } else {
            $loan_linked_charge->amount = $loan_charge->amount;
        }
        $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
        $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
        $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
        $loan_linked_charge->save();
        //find schedule to apply this charge
        $repayment_schedule = $loan->repayment_schedules->where('due_date', '>=', $request->date)->where('from_date', '<=', $request->date)->first();
        if (empty($repayment_schedule)) {
            if (Carbon::parse($request->date)->lessThan($loan->first_payment_date)) {
                $repayment_schedule = $loan->repayment_schedules->first();
            } else {
                $repayment_schedule = $loan->repayment_schedules->last();
            }

        }
        //calculate the amount
        if ($loan_linked_charge->loan_charge_option_id == 1) {
            $amount = $loan_linked_charge->amount;
        }
        if ($loan_linked_charge->loan_charge_option_id == 2) {
            $amount = round(($loan_linked_charge->amount * ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived) / 100), $loan->decimals);
        }
        if ($loan_linked_charge->loan_charge_option_id == 3) {
            $amount = round(($loan_linked_charge->amount * (($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) + ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived)) / 100), $loan->decimals);
        }
        if ($loan_linked_charge->loan_charge_option_id == 4) {
            $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) / 100), $loan->decimals);
        }
        if ($loan_linked_charge->loan_charge_option_id == 5) {
            $amount = round(($loan_linked_charge->amount * ($loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_repaid_derived') - $loan->repayment_schedules->sum('principal_written_off_derived')) / 100), $loan->decimals);
        }
        if ($loan_linked_charge->loan_charge_option_id == 6) {
            $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
        }
        if ($loan_linked_charge->loan_charge_option_id == 7) {
            $amount = round(($loan_linked_charge->amount * $loan->principal / 100), $loan->decimals);
        }
        $repayment_schedule->fees = $repayment_schedule->fees + $amount;
        $repayment_schedule->save();
        $loan_linked_charge->calculated_amount = $amount;
        $loan_linked_charge->due_date = $repayment_schedule->due_date;
        //create transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
        $loan_transaction->loan_transaction_type_id = 10;
        $loan_transaction->submitted_on = $repayment_schedule->due_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $loan_linked_charge->calculated_amount;
        $loan_transaction->due_date = $repayment_schedule->due_date;
        $loan_transaction->debit = $loan_linked_charge->calculated_amount;
        $loan_transaction->reversible = 1;
        $loan_transaction->save();
        $loan_linked_charge->loan_transaction_id = $loan_transaction->id;
        $loan_linked_charge->save();
        activity()->on($loan_charge)
            ->withProperties(['id' => $loan_charge->id])
            ->log('Create Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $id . '/show');
    }

    public function waive_interest(Request $request, $id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        $request->validate([
            'interest_waived_amount' => ['required'],
            'date' => ['required', 'date'],
        ]);

        //find schedule to apply this charge
        $repayment_schedule = $loan->repayment_schedules->where('due_date', '>=', $request->date)->where('from_date', '<=', $request->date)->first();
        if (empty($repayment_schedule)) {
            if (Carbon::parse($request->date)->lessThan($loan->first_payment_date)) {
                $repayment_schedule = $loan->repayment_schedules->first();
            } else {
                $repayment_schedule = $loan->repayment_schedules->last();
            }

        }
        $amount = $request->interest_waived_amount;
        foreach ($loan->repayment_schedules->where('due_date', '>=', $repayment_schedule->due_date) as $repayment_schedule) {
            $interest = $repayment_schedule->interest - $repayment_schedule->interest_written_off_derived - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived;
            if ($interest <= 0) {
                continue;
            }
            if ($amount >= $interest) {
                $repayment_schedule->interest_waived_derived = $repayment_schedule->interest_waived_derived + $interest;
                $amount = $amount - $interest;
            } else {
                $repayment_schedule->interest_waived_derived = $repayment_schedule->interest_waived_derived + $amount;
                $amount = 0;
            }
            $repayment_schedule->save();
            if ($amount <= 0) {
                break;
            }
        }
        $repayment_schedule->fees = $repayment_schedule->fees + $amount;
        $repayment_schedule->save();
        //create transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.waive', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.interest', 1);
        $loan_transaction->loan_transaction_type_id = 4;
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $request->interest_waived_amount;
        $loan_transaction->credit = $request->interest_waived_amount;
        $loan_transaction->reversible = 0;
        $loan_transaction->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Waive Loan Interest');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $id . '/show');
    }

    public function reverse_repayment(Request $request, $id)
    {

        $loan_transaction = LoanTransaction::find($id);
        $loan = $loan_transaction->loan;

        $loan_transaction->amount = 0;
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->reversed = 1;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Reverse Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function create_loan_calculator()
    {
        $loan_products = LoanProduct::where('active', 1)->get();
        JavaScript::put([
            'loan_products' => $loan_products
        ]);
        return theme_view('loan::loan_calculator.create', compact('loan_products'));
    }

    public function process_loan_calculator(Request $request)
    {
        $loan_product = LoanProduct::with('charges')->with('charges.charge')->find($request->loan_product_id);
        $loan_details = [];
        $loan_details['principal'] = $request->applied_amount;
        $loan_details['disbursement_date'] = $request->expected_disbursement_date;

        $schedules = [];
        $loan_principal = $request->applied_amount;
        $interest_rate = determine_period_interest_rate($request->interest_rate, $request->repayment_frequency_type, $request->interest_rate_type);
        $balance = round($loan_principal, $loan_product->decimals);
        $period = ($request->loan_term / $request->repayment_frequency);
        $payment_from_date = $request->expected_disbursement_date;
        $next_payment_date = $request->expected_first_payment_date;
        $total_principal = 0;
        $total_interest = 0;
        for ($i = 1; $i <= $period; $i++) {
            $schedule = [];

            $schedule['installment'] = $i;
            $schedule['due_date'] = $next_payment_date;
            $schedule['from_date'] = $payment_from_date;
            $schedule['fees'] = 0;

            //flat  method
            if ($loan_product->interest_methodology == 'flat') {
                $principal = round($loan_principal / $period, $loan_product->decimals);
                $interest = round($interest_rate * $loan_principal, $loan_product->decimals);
                if ($loan_product->grace_on_interest_charged >= $i) {
                    $schedule['interest'] = 0;
                } else {
                    $schedule['interest'] = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $schedule['principal'] = round($balance, $loan_product->decimals);
                } else {
                    $schedule['principal'] = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan_product->interest_methodology == 'declining_balance') {
                if ($loan_product->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $loan_principal, $period), $loan_product->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan_product->decimals);
                    $principal = round(($amortized_payment - $interest), $loan_product->decimals);
                    if ($loan_product->grace_on_interest_charged >= $i) {
                        $schedule['interest'] = 0;
                    } else {
                        $schedule['interest'] = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $schedule['principal'] = round($balance, $loan_product->decimals);
                    } else {
                        $schedule['principal'] = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan_product->amortization_method == 'equal_principal_payments') {
                    $principal = round($loan_principal / $period, $loan_product->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan_product->decimals);
                    if ($loan_product->grace_on_interest_charged >= $i) {
                        $schedule['interest'] = 0;
                    } else {
                        $schedule['interest'] = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $schedule['principal'] = round($balance, $loan_product->decimals);
                    } else {
                        $schedule['principal'] = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }

            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = Carbon::parse($next_payment_date)->add($loan_product->repayment_frequency, $loan_product->repayment_frequency_type)->format("Y-m-d");
            $total_principal = $total_principal + $schedule['principal'];
            $total_interest = $total_interest + $schedule['interest'];
            $schedules[] = $schedule;
        }

        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($loan_product->charges as $key) {
            //disbursement

            if ($key->charge->loan_charge_type_id == 1) {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;

                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $loan_product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), $loan_product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);

                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), $loan_product->decimals);

                }
                $disbursement_fees = $disbursement_fees + $amount;
            }
            //installment_fee
            if ($key->charge->loan_charge_type_id == 3) {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), $loan_product->decimals);
                }
                $installment_fees = $installment_fees + $amount;
                //add the charges to the schedule
                foreach ($schedules as &$temp) {
                    if ($key->charge->loan_charge_option_id == 2) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['principal'] / 100), $loan_product->decimals);
                    } elseif ($key->charge->loan_charge_option_id == 3) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * ($temp['interest'] + $temp['principal']) / 100), $loan_product->decimals);
                    } elseif ($key->charge->loan_charge_option_id == 4) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['interest'] / 100), $loan_product->decimals);
                    } else {
                        $temp['fees'] = $temp['fees'] + $key->charge->amount;
                    }

                }

            }

        }
        $loan_details['total_interest'] = $total_interest;
        $loan_details['decimals'] = $loan_product->decimals;
        $loan_details['disbursement_fees'] = $disbursement_fees;
        $loan_details['total_fees'] = $disbursement_fees + $installment_fees;
        $loan_details['total_due'] = $disbursement_fees + $installment_fees + $total_interest + $total_principal;
        $loan_details['maturity_date'] = $next_payment_date;
        activity()->log('Use Loan Calculator');
        return theme_view('loan::loan_calculator.show', compact('loan_details', 'schedules'));
    }

    public function approve_application(Request $request)
    {
        $loan_application = LoanApplication::with('loan_product')->with('client')->first();
        $client = $loan_application->client;
        $loan_product = $loan_application->loan_product;
        $funds = Fund::all();
        $loan_purposes = LoanPurpose::get();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $charges = [];
        $charges_list = [];
        foreach ($loan_product->charges as $key) {
            if (!empty($key->charge)) {
                //charge type
                array_push($charges_list, ['id' => $key->charge->id, 'text' => $key->charge->name]);
                if ($key->charge->loan_charge_type_id == 1) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement', 1);
                }
                if ($key->charge->loan_charge_type_id == 2) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.specified_due_date', 1);
                }
                if ($key->charge->loan_charge_type_id == 3) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 4) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 5) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement_paid_with_repayment', 1);
                }
                if ($key->charge->loan_charge_type_id == 6) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.loan_rescheduling_fee', 1);
                }
                if ($key->charge->loan_charge_type_id == 7) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue_on_loan_maturity', 1);
                }
                if ($key->charge->loan_charge_type_id == 8) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.last_installment_fee', 1);
                }
                //charge option
                if ($key->charge->loan_charge_option_id == 1) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.flat', 1);
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.total_outstanding_loan_principal', 1);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.original_loan_principal', 1);
                }
                $charges[$key->charge->id] = $key;

            }
        }
        JavaScript::put([
            'loan_product' => $loan_product,
            'charges' => $charges,
            'original_charges' => $charges,
            'charges_list' => $charges_list

        ]);

        return theme_view('loan::application.approve', compact('client', 'loan_product', 'users', 'loan_purposes', 'funds', 'loan_application'));

    }

    public function store_approve_application(Request $request, $id)
    {

        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'client_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'interest_rate' => ['required', 'numeric'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $loan_product = LoanProduct::find($request->loan_product_id);
        $client = Client::find($request->client_id);
        $loan = new Loan();
        $loan->currency_id = $loan_product->currency_id;
        $loan->loan_product_id = $loan_product->id;
        $loan->client_id = $client->id;
        $loan->branch_id = $client->branch_id;
        $loan->loan_transaction_processing_strategy_id = $loan_product->loan_transaction_processing_strategy_id;
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->created_by_id = Auth::id();
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_rate_type = $loan_product->interest_rate_type;
        $loan->grace_on_principal_paid = $loan_product->grace_on_principal_paid;
        $loan->grace_on_interest_paid = $loan_product->grace_on_interest_paid;
        $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
        $loan->interest_methodology = $loan_product->interest_methodology;
        $loan->amortization_method = $loan_product->amortization_method;
        $loan->auto_disburse = $loan_product->auto_disburse;
        $loan->submitted_on_date = date("Y-m-d");
        $loan->submitted_by_user_id = Auth::id();
        $loan->save();
        //save charges
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $loan_charge = LoanCharge::find($key);
                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $key;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $value;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
            }
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Created';
        $loan_history->save();
        $loan_officer_history = new LoanOfficerHistory();
        $loan_officer_history->loan_id = $loan->id;
        $loan_officer_history->created_by_id = Auth::id();
        $loan_officer_history->loan_officer_id = $request->loan_officer_id;
        $loan_officer_history->start_date = date("Y-m-d");
        $loan_officer_history->save();
        //update loan application
        $loan_application = LoanApplication::find($id);
        $loan_application->status = 'approved';
        $loan_application->loan_id = $loan->id;
        $loan_application->save();
        activity()->on($loan_application)
            ->withProperties(['id' => $loan_application->id])
            ->log('Approve Loan Application');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        //fire loan status changed event
        event(new LoanStatusChanged($loan));
        return redirect('loan/' . $loan->id . '/show');
    }

    public function reject_application(Request $request, $id)
    {

        $loan_application = LoanApplication::find($id);
        $loan_application->status = 'rejected';
        $loan_application->save();
        activity()->on($loan_application)
            ->withProperties(['id' => $loan_application->id])
            ->log('Reject Loan Application');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function undo_reject_application(Request $request, $id)
    {

        $loan_application = LoanApplication::find($id);
        $loan_application->status = 'pending';
        $loan_application->save();
        activity()->on($loan_application)
            ->withProperties(['id' => $loan_application->id])
            ->log('Undo Loan Application Rejection');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }
}
