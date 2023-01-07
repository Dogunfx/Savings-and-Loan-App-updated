<?php
/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 10/12/2018
 * Time: 20:35
 */

namespace App\Http\Controllers\Portal;


use App\Events\RepaymentCreated;
use App\Helpers\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Mail\LoanSchedule;
use App\Models\Borrower;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentMethod;
use App\Models\LoanTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Paynow\Payments\Paynow;
use Stripe\Stripe;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel']);
    }

    public function index(Request $request)
    {

        if (!empty($request->status)) {
            $status = $request->status;
        } else {
            $status = "";
        }

        return view('portal.loan.data', compact('status'));
    }

    public function get_loans(Request $request)
    {
        if (!empty($request->status)) {
            $status = $request->status;
        } else {
            $status = null;
        }
        if (!empty($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = null;
        }

        $query = DB::table("loans")->leftJoin("borrowers", "borrowers.id", "loans.borrower_id")->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")->selectRaw(DB::raw("borrowers.first_name,borrowers.last_name,loans.id,loans.borrower_id,loans.principal,loans.disbursed_date,loan_products.name loan_product,loans.status,loans.interest_rate,loans.interest_period,(SELECT SUM(principal) FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_principal,(SELECT SUM(interest)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_interest,(SELECT SUM(fees)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_fees,(SELECT SUM(penalty)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loans.id) payments"))->where('loans.borrower_id', session('borrower_id'))->when($status, function ($query) use ($status) {
            $query->where("loans.status", $status);
        })->groupBy("loans.id");
        return DataTables::of($query)->editColumn('borrower', function ($data) {
            return '<a href="' . url('borrower/' . $data->borrower_id . '/show') . '">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })->editColumn('interest_rate', function ($data) {
            return $data->interest_rate . '%/' . $data->interest_period;
        })->editColumn('principal', function ($data) {
            return number_format($data->principal, 2);
        })->editColumn('total_principal', function ($data) {
            return number_format($data->total_principal - $data->total_principal_waived, 2);
        })->editColumn('total_interest', function ($data) {
            return number_format($data->total_interest - $data->total_interest_waived, 2);
        })->editColumn('total_fees', function ($data) {
            return number_format($data->total_fees - $data->total_fees_waived, 2);
        })->editColumn('total_penalty', function ($data) {
            return number_format($data->total_penalty - $data->total_penalty_waived, 2);
        })->editColumn('due', function ($data) {
            return number_format($data->total_principal - $data->total_principal_waived + $data->total_interest - $data->total_interest_waived + $data->total_fees - $data->total_fees_waived + $data->total_penalty - $data->total_penalty_waived, 2);
        })->editColumn('payments', function ($data) {
            return number_format($data->payments, 2);
        })->editColumn('balance', function ($data) {
            return number_format($data->total_principal - $data->total_principal_waived + $data->total_interest - $data->total_interest_waived + $data->total_fees - $data->total_fees_waived + $data->total_penalty - $data->total_penalty_waived - $data->payments, 2);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('general.awaiting', 1) . ' ' . trans_choice('general.disbursement', 1) . '</span>';
            }
            if ($data->status == 'disbursed') {
                return '<span class="label label-info">' . trans_choice('general.active', 1) . '</span>';
            }
            if ($data->status == 'declined') {
                return '<span class="label label-danger">' . trans_choice('general.declined', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'written_off') {
                return '<span class="label label-danger">' . trans_choice('general.written_off', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('general.closed', 1) . '</span>';
            }
            if ($data->status == 'pending_reschedule') {
                return '<span class="label label-warning">' . trans_choice('general.pending_reschedule', 1) . '</span>';
            }
            if ($data->status == 'rescheduled') {
                return '<span class="label label-info">' . trans_choice('general.rescheduled', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('portal/loan/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('portal/loan/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'borrower', 'action', 'status'])->make(true);
    }

    public function index_repayment()
    {

        return view('portal.loan_repayment.data', compact('data'));
    }

    public function get_transactions(Request $request)
    {

        if (!empty($request->transaction_type)) {
            $transaction_type = $request->transaction_type;
        } else {
            $transaction_type = null;
        }
        if (!empty($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = null;
        }
        if (!empty($request->reversed)) {
            $reversed = $request->reversed;
        } else {
            $reversed = null;
        }

        $query = DB::table("loan_transactions")->leftJoin("borrowers", "borrowers.id", "loan_transactions.borrower_id")->leftJoin("users", "users.id", "loan_transactions.user_id")->leftJoin("loan_repayment_methods", "loan_repayment_methods.id", "loan_transactions.repayment_method_id")->selectRaw(DB::raw("borrowers.first_name borrower_first_name,borrowers.last_name borrower_last_name,users.first_name user_first_name,users.last_name user_last_name,loan_repayment_methods.name repayment_method,loan_transactions.*"))->where('loan_transactions.borrower_id', session('borrower_id'))->when($transaction_type, function ($query) use ($transaction_type) {
            $query->where("loan_transactions.transaction_type", $transaction_type);
        })->when($reversed, function ($query) use ($reversed) {
            $query->where("loan_transactions.reversed", $reversed);
        });
        return DataTables::of($query)->editColumn('borrower', function ($data) {
            return '<a href="' . url('borrower/' . $data->borrower_id . '/show') . '">' . $data->borrower_first_name . ' ' . $data->borrower_last_name . '</a>';
        })->editColumn('collected_by', function ($data) {
            return $data->user_first_name . ' ' . $data->user_last_name;
        })->editColumn('debit', function ($data) {
            return number_format($data->debit, 2);
        })->editColumn('credit', function ($data) {
            return number_format($data->credit, 2);
        })->editColumn('repayment_method', function ($data) {
            return $data->repayment_method;
        })->editColumn('transaction_type', function ($data) {
            if ($data->transaction_type == 'disbursement') {
                return trans_choice('general.disbursement', 1);
            }
            if ($data->transaction_type == 'specified_due_date_fee') {
                return trans_choice('general.specified_due_date', 1) . ' ' . trans_choice('general.fee', 1);
            }
            if ($data->transaction_type == 'installment_fee') {
                return trans_choice('general.installment_fee', 1);
            }
            if ($data->transaction_type == 'overdue_installment_fee') {
                return trans_choice('general.overdue_installment_fee', 1);
            }
            if ($data->transaction_type == 'loan_rescheduling_fee') {
                return trans_choice('general.loan_rescheduling_fee', 1);
            }
            if ($data->transaction_type == 'overdue_maturity') {
                return trans_choice('general.overdue_maturity', 1);
            }
            if ($data->transaction_type == 'disbursement_fee') {
                return trans_choice('general.disbursement', 1) . ' ' . trans_choice('general.charge', 1);
            }
            if ($data->transaction_type == 'interest') {
                return trans_choice('general.interest', 1) . ' ' . trans_choice('general.applied', 1);
            }
            if ($data->transaction_type == 'repayment') {
                return trans_choice('general.repayment', 1);
            }
            if ($data->transaction_type == 'penalty') {
                return trans_choice('general.penalty', 1);
            }
            if ($data->transaction_type == 'interest_waiver') {
                return trans_choice('general.interest', 1) . ' ' . trans_choice('general.waiver', 2);
            }
            if ($data->transaction_type == 'charge_waiver') {
                return trans_choice('general.charge', 1) . ' ' . trans_choice('general.waiver', 2);
            }
            if ($data->transaction_type == 'write_off') {
                return trans_choice('general.write_off', 1);
            }
            if ($data->transaction_type == 'write_off_recovery') {
                return trans_choice('general.recovery', 1) . ' ' . trans_choice('general.repayment', 1);
            }
        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('portal/loan/transaction/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            if ($data->transaction_type == 'repayment') {
                $action .= '<li><a href="' . url('portal/loan/transaction/' . $data->id . '/print') . '" class="" target="_blank">' . trans_choice('general.print', 2) . ' ' . trans_choice('general.receipt', 1) . '</a></li>';
                $action .= '<li><a href="' . url('portal/loan/transaction/' . $data->id . '/pdf') . '" class="" target="_blank">' . trans_choice('general.pdf', 2) . ' ' . trans_choice('general.receipt', 1) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('portal/loan/transaction/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->editColumn('loan_id', function ($data) {
            return '<a href="' . url('portal/loan/' . $data->loan_id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'loan_id', 'borrower', 'collected_by', 'action'])->make(true);
    }

    public function show($loan)
    {
        if (session('borrower_id') != $loan->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        $schedules = \App\Models\LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $custom_fields = CustomFieldMeta::where('category', 'loans')->where('parent_id', $loan->id)->get();
        return view('portal.loan.show',
            compact('loan', 'schedules', 'payments', 'custom_fields', 'loan_disbursed_by', 'guarantors'));
    }

    //transactions
    public function showTransaction($loan_transaction)
    {

        $custom_fields = CustomFieldMeta::where('category', 'repayments')->where('parent_id',
            $loan_transaction->id)->get();
        return view('portal.loan_transaction.show', compact('loan_transaction', 'custom_fields'));
    }

    public function pdfTransaction($loan_transaction)
    {
        $pdf = PDF::loadView('portal.loan_transaction.pdf', compact('loan_transaction'));
        return $pdf->download($loan_transaction->borrower->title . ' ' . $loan_transaction->borrower->first_name . ' ' . $loan_transaction->borrower->last_name . " - Repayment Receipt.pdf");
    }

    public function printTransaction($loan_transaction)
    {

        return view('portal.loan_transaction.print', compact('loan_transaction'));
    }

    public function pdfSchedule($loan)
    {

        $schedules = \App\Models\LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $pdf = PDF::loadView('loan.pdf_schedule', compact('loan', 'schedules'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Repayment Schedule.pdf");

    }

    public function printSchedule($loan)
    {
        if (session('borrower_id') != $loan->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $schedules = \App\Models\LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        return view('portal.loan.print_schedule', compact('loan', 'schedules'));
    }

    public function pdfLoanStatement($loan)
    {
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        $pdf = PDF::loadView('portal.loan.pdf_loan_statement', compact('loan', 'payments'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Statement.pdf");

    }

    public function printLoanStatement($loan)
    {
        if (session('borrower_id') != $loan->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        return view('loan.print_loan_statement', compact('loan', 'payments'));
    }

    public function pdfBorrowerStatement($borrower)
    {
        if (session('borrower_id') != $borrower->id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        $pdf = PDF::loadView('portal.loan.pdf_borrower_statement', compact('loans'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Client Statement.pdf");

    }

    public function printBorrowerStatement($borrower)
    {
        if (session('borrower_id') != $borrower->id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        return view('portal.loan.print_borrower_statement', compact('loans'));
    }

    //loan applications
    public function index_application()
    {

        $data = LoanApplication::where('borrower_id', session('borrower_id'))->get();

        return view('portal.loan.applications', compact('data'));
    }

    public function get_applications(Request $request)
    {
        if (!empty($request->status)) {
            $status = $request->status;
        } else {
            $status = null;
        }
        if (!empty($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = null;
        }

        $query = DB::table("loan_applications")->leftJoin("borrowers", "borrowers.id", "loan_applications.borrower_id")->leftJoin("loan_products", "loan_products.id", "loan_applications.loan_product_id")->selectRaw(DB::raw("borrowers.first_name,borrowers.last_name,loan_applications.id,loan_applications.borrower_id,loan_applications.amount,loan_applications.created_at,loan_products.name loan_product,loan_applications.status"))->where('loan_applications.borrower_id', session('borrower_id'))->when($status, function ($query) use ($status) {
            $query->where("loan_applications.status", $status);
        });
        return DataTables::of($query)->editColumn('borrower', function ($data) {
            return '<a href="' . url('borrower/' . $data->borrower_id . '/show') . '">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })->editColumn('amount', function ($data) {
            return number_format($data->amount, 2);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('general.awaiting', 1) . ' ' . trans_choice('general.disbursement', 1) . '</span>';
            }
            if ($data->status == 'disbursed') {
                return '<span class="label label-info">' . trans_choice('general.active', 1) . '</span>';
            }
            if ($data->status == 'declined') {
                return '<span class="label label-danger">' . trans_choice('general.declined', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'written_off') {
                return '<span class="label label-danger">' . trans_choice('general.written_off', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('general.closed', 1) . '</span>';
            }
            if ($data->status == 'pending_reschedule') {
                return '<span class="label label-warning">' . trans_choice('general.pending_reschedule', 1) . '</span>';
            }
            if ($data->status == 'rescheduled') {
                return '<span class="label label-info">' . trans_choice('general.rescheduled', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('portal/loan/application/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            if ($data->status == 'pending') {
                $action .= '<li><a href="' . url('portal/loan/application/' . $data->id . '/edit') . '" class="">' . trans_choice('general.edit', 2) . '</a></li>';
                $action .= '<li><a href="' . url('portal/loan/application/' . $data->id . '/delete') . '" class="delete">' . trans_choice('general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('portal/loan/application/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'borrower', 'action', 'status'])->make(true);
    }

    public function create_application()
    {
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }
        return view('portal.loan.apply', compact('loan_products'));
    }

    public function store_application(Request $request)
    {
        $borrower = Borrower::find(session('borrower_id'));
        $application = new LoanApplication();
        $application->status = "pending";
        $application->loan_product_id = $request->loan_product_id;
        $application->branch_id = $borrower->branch_id;
        $application->borrower_id = $borrower->id;
        $application->amount = $request->amount;
        $application->notes = $request->notes;
        $application->save();
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('portal/loan/application/data');
    }

    public function edit_application($loan_application)
    {
        if ($loan_application->status != 'pending') {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if (session('borrower_id') != $loan_application->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }
        return view('portal.loan.edit_application', compact('loan_products', 'loan_application'));
    }

    public function update_application(Request $request, $id)
    {
        $application = LoanApplication::find($id);
        if (session('borrower_id') != $application->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if ($application->status != 'pending') {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $application->amount = $request->amount;
        $application->notes = $request->notes;
        $application->save();
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('portal/loan/application/data');
    }

    public function delete_application($id)
    {
        $application = LoanApplication::find($id);
        if (session('borrower_id') != $application->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if ($application->status != 'pending') {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        LoanApplication::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

    public function show_application($loan_application)
    {

        if (session('borrower_id') != $loan_application->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('portal.loan.show_application', compact('loan_products', 'loan_application'));
    }

    public function create_repayment($loan)
    {
        if (session('borrower_id') != $loan->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::where('is_system', 1)->where('active', 1)->get() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }

        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('portal.loan_repayment.create', compact('loan', 'repayment_methods', 'custom_fields'));
    }

    public function get_repayment_method_details($loan_repayment_method)
    {
        return json_encode($loan_repayment_method);
    }

//stripe payment
    public function stripe_payment(Request $request, $loan)
    {
        $payment_method = LoanRepaymentMethod::find($request->repayment_method_id);
        $stripe = array(
            "secret_key" => $payment_method->field2,
            "publishable_key" => $payment_method->field1
        );

        $json = array();
        Stripe::setApiKey($stripe['secret_key']);
        try {

            $token = $request->token;
            $customer = \Stripe\Customer::create(array(
                'email' => $loan->borrower->email,
                'source' => $token
            ));
            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount' => $request->amount * 100,
                'currency' => 'usd',
            ));
            //payment successful
            $loan_transaction = new LoanTransaction();
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->receipt = $charge["id"];
            $loan_transaction->date = date("Y-m-d");
            $loan_transaction->reversible = 1;
            $loan_transaction->repayment_method_id = $request->repayment_method_id;
            $date = explode('-', date("Y-m-d"));
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $charge["amount"] / 100;
            $loan_transaction->notes = "Paid via Stripe";
            $loan_transaction->save();
            //fire payment added event
            //update loan status if need be
            event(new RepaymentCreated($loan_transaction));
            if (round(GeneralHelper::loan_total_balance($loan->id)) <= 0) {
                $l = Loan::find($loan->id);
                $l->status = "closed";
                $l->save();
            }
            $json["success"] = 1;
            $json["msg"] = "Successfully Paid";
        } catch (\Exception $e) {
            $json["success"] = 0;
            $json["msg"] = "An error occurred";
        }


    }

    public function paynow_payment(Request $request, $loan)
    {
        $payment_method = LoanRepaymentMethod::find($request->repayment_method_id);
        $paynow = new Paynow(
            $payment_method->field1,
            $payment_method->field2,
            url('portal/payment/loan/pay/paynow/result'),
            url('portal/loan/' . $loan->id . '/show')
        );
        $payment = $paynow->createPayment($loan->id, $loan->borrower->email);
        $payment->add("Loan Repayment", $request->amount);
        $response = $paynow->send($payment);
        if ($response->success()) {
            // Redirect the user to Paynow


            // Or if you prefer more control, get the link to redirect the user to, then use it as you see fit
            //$link = $response->redirectLink();

            // Get the poll url (used to check the status of a transaction). You might want to save this in your DB
            //$pollUrl = $response->pollUrl();
            return redirect($response->redirectUrl());
        }

    }
}