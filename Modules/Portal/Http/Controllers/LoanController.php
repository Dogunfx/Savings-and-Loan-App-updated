<?php

namespace Modules\Portal\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentGateway;
use Modules\Core\Entities\PaymentType;
use Modules\CustomField\Entities\CustomField;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanApplication;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\TransactionUpdated;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class LoanController extends Controller
{
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
        $loan_officer_id = $request->loan_officer_id;
        $branch_id = $request->branch_id;
        $data = Loan::leftJoin("clients", "clients.id", "loans.client_id")
            ->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")
            ->leftJoin("branches", "branches.id", "loans.branch_id")
            ->leftJoin("users", "users.id", "loans.loan_officer_id")
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
            ->where("loans.client_id", session('client_id'))
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.applied_amount,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")
            ->groupBy("loans.id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('portal::loan.index', compact('data'));
    }

    public function get_loans(Request $request)
    {

        $status = $request->status;
        $client_id = session('client_id');
        $loan_officer_id = $request->loan_officer_id;

        $query = DB::table("loans")->leftJoin("clients", "clients.id", "loans.client_id")->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")->leftJoin("branches", "branches.id", "loans.branch_id")->leftJoin("users", "users.id", "loans.loan_officer_id")->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")->when($status, function ($query) use ($status) {
            $query->where("loans.status", $status);
        })->when($client_id, function ($query) use ($client_id) {
            $query->where("loans.client_id", $client_id);
        })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
            $query->where("loans.loan_officer_id", $loan_officer_id);
        })->groupBy("loans.id");
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('portal/client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
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

            $action = '<a href="' . url('portal/loan/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('portal/loan/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'client', 'action', 'status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('portal::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {

        $loan = Loan::with('repayment_schedules')->with('transactions')->with('charges')->with('client')->with('loan_product')->with('notes')->with('guarantors')->with('files')->with('collateral')->with('collateral.collateral_type')->with('notes.created_by')->find($id);
        if ($loan->client_id != session('client_id')) {
            Flash::warning(trans('core::general.permission_denied'));
            return redirect()->back();
        }
        return theme_view('portal::loan.show', compact('loan'));
    }

    //transactions
    public function show_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        return theme_view('portal::loan.loan_transaction.show', compact('loan_transaction'));
    }

    public function pdf_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        $pdf = PDF::loadView('portal::loan.loan_transaction.pdf', compact('loan_transaction'));
        return $pdf->download(trans_choice('loan::general.transaction', 1) . ' ' . trans_choice('loan::general.detail', 2) . ".pdf");
    }

    public function print_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        return theme_view('portal::loan.loan_transaction.print', compact('loan_transaction'));
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
        $pdf = PDF::loadView('portal::loan.loan_schedule.pdf', compact('loan'))->setPaper('a4', 'landscape');
        return $pdf->download(trans_choice('loan::general.repayment', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf");
    }

    public function print_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        return theme_view('portal::loan.loan_schedule.print', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('portal::edit');
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
            })
            ->when($branch_id, function ($query) use ($branch_id) {
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
            ->where('client_id', session('client_id'))
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) created_by,loan_applications.id,loan_applications.client_id,loan_products.name loan_product,loan_applications.status,loan_applications.loan_id,branches.name branch,loan_applications.amount,loan_applications.created_at")
            ->groupBy("loan_applications.id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('portal::loan.application.index', compact('data'));
    }

    public function create_application()
    {
        $loan_products = LoanProduct::where('active', 1)->get();
        \JavaScript::put([
            'loan_products' => $loan_products
        ]);
        return theme_view('portal::loan.application.create', compact('loan_products'));
    }

    public function store_application(Request $request)
    {
        $client = Client::find(session('client_id'));
        if (empty($client)) {
            Flash::warning(trans('loan::general.client_not_found_please_logout'));
            return redirect()->back();
        }
        $request->validate([
            'loan_product_id' => ['required'],
            'amount' => ['required', 'numeric'],
        ]);
        $loan_application = new LoanApplication();
        $loan_application->client_id = $client->id;
        $loan_application->created_by_id = Auth::id();
        $loan_application->branch_id = $client->branch_id;
        $loan_application->loan_product_id = $request->loan_product_id;
        $loan_application->amount = $request->amount;
        $loan_application->notes = $request->notes;
        $loan_application->save();
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('portal/loan/application');
    }

    public function destroy_application(Request $request, $id)
    {
        $client = Client::find(session('client_id'));
        $loan_application = LoanApplication::find($id);
        if (empty($client)) {
            Flash::warning(trans('loan::general.client_not_found_please_logout'));
            return redirect()->back();
        }
        if ($loan_application->client_id != session('client_id')) {
            Flash::warning(trans('core::general.permission_denied'));
            return redirect()->back();
        }
        if ($loan_application->status != 'pending') {
            Flash::warning(trans('core::general.permission_denied'));
            return redirect()->back();
        }
        $loan_application->delete();
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('portal/loan/application');
    }

//repayments
    public function create_repayment($id)
    {
        $payment_types = PaymentType::where('is_online',1)->where('active',1)->get();
        $custom_fields = CustomField::where('category', 'add_repayment')->where('active', 1)->get();
        return theme_view('portal::loan.loan_repayment.create', compact('id', 'custom_fields', 'payment_types'));
    }

    public function store_repayment(Request $request, $id)
    {
        $loan = Loan::with('loan_product')->find($id);
        $client = Client::find(session('client_id'));
        if (empty($client)) {
            Flash::warning(trans('loan::general.client_not_found_please_logout'));
            return redirect()->back();
        }
        if ($loan->client_id != session('client_id')) {
            Flash::warning(trans('core::general.permission_denied'));
            return redirect()->back();
        }
        $request->validate([
            'amount' => ['required', 'numeric'],
            'payment_gateway' => ['required'],
        ]);

        $class = 'Modules\\' . $request->payment_gateway . '\\' . $request->payment_gateway;
        $class = new $class;
        $response = $class->processPayment([
            'id' => $id,
            'amount' => $request->amount,
            'module' => 'loan',
            'return_url' => url('portal/loan/' . $id . '/show')
        ]);
        if($response instanceof Response){
            return $response;
        }
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('portal/loan/' . $id . '/show');
    }
}
