<?php

namespace App\Http\Controllers;

use App\Events\InterestWaived;
use App\Events\LoanTransactionUpdated;
use App\Events\RepaymentCreated;
use App\Events\RepaymentReversed;
use App\Events\RepaymentUpdated;
use App\Events\UpdateLoanTransactions;
use App\Helpers\GeneralHelper;
use App\Listeners\UpdateLoanTransaction;
use App\Mail\BorrowerStatement;
use App\Mail\LoanStatement;
use App\Models\BankAccount;
use App\Models\Capital;
use App\Models\ChartOfAccount;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Email;
use App\Models\Guarantor;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanCharge;
use App\Models\LoanFee;
use App\Models\LoanFeeMeta;
use App\Models\LoanGuarantor;
use App\Models\LoanOverduePenalty;
use App\Models\LoanProduct;
use App\Models\LoanProductCharge;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentMethod;
use App\Models\LoanDisbursedBy;
use App\Models\Borrower;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\Setting;
use App\Models\Sms;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PDF;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if (!empty($request->status)) {
            $status = $request->status;
        } else {
            $status = "";
        }

        return view('loan.data', compact('status'));
    }

    public function get_loans(Request $request)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
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

        $query = DB::table("loans")->leftJoin("borrowers", "borrowers.id", "loans.borrower_id")->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")->selectRaw(DB::raw("borrowers.first_name,borrowers.last_name,loans.id,loans.borrower_id,loans.principal,loans.disbursed_date,loan_products.name loan_product,loans.status,loans.interest_rate,loans.interest_period,(SELECT SUM(principal) FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_principal,(SELECT SUM(interest)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_interest,(SELECT SUM(fees)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_fees,(SELECT SUM(penalty)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_penalty,(SELECT SUM(principal_waived)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_principal_waived,(SELECT SUM(interest_waived)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_interest_waived,(SELECT SUM(fees_waived) total_fees_waived FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_fees_waived,(SELECT SUM(penalty_waived)  FROM loan_schedules WHERE loan_schedules.loan_id=loans.id) total_penalty_waived,(SELECT SUM(credit) FROM loan_transactions WHERE transaction_type='repayment' AND reversed=0 AND loan_transactions.loan_id=loans.id) payments"))->where('loans.branch_id', session('branch_id'))->when($status, function ($query) use ($status) {
            $query->where("loans.status", $status);
        })->when($borrower_id, function ($query) use ($borrower_id) {
            $query->where("loans.borrower_id", $borrower_id);
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
            if (Sentinel::hasAccess('loans.view')) {
                $action .= '<li><a href="' . url('loan/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            }
            if (Sentinel::hasAccess('loans.update')) {
                $action .= '<li><a href="' . url('loan/' . $data->id . '/edit') . '" class="">' . trans_choice('general.edit', 2) . '</a></li>';
            }
            if (Sentinel::hasAccess('loans.delete')) {
                $action .= '<li><a href="' . url('loan/' . $data->id . '/delete') . '" class="delete">' . trans_choice('general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'borrower', 'action', 'status'])->make(true);
    }

    public function pending_approval(Request $request)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if (empty($request->status)) {
            $data = Loan::all();
        } else {
            $data = Loan::where('branch_id', session('branch_id'))->where('status', $request->status)->get();
        }

        return view('loan.data', compact('data'));
    }

    public function create(Request $request)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        $users = [];
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }
        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }
        if (isset($request->product_id)) {
            $loan_product = LoanProduct::find($request->product_id);
        } else {
            $loan_product = LoanProduct::first();
        }
        if (isset($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = '';
        }
        if (empty($loan_product)) {
            Flash::warning("No loan product set. You must first set a loan product");
            return redirect()->back();
        }
        $charges = array();
        foreach (LoanProductCharge::where('loan_product_id', $loan_product->id)->get() as $key) {
            if (!empty($key->charge)) {
                $charges[$key->id] = $key->charge->name;
            }

        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        return view('loan.create',
            compact('borrowers', 'loan_disbursed_by', 'loan_products', 'loan_product', 'borrower_id', 'custom_fields',
                'charges', 'loan_overdue_penalties', 'users'));
    }

    public function reschedule(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.reschedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }

        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }
        if (isset($request->product_id)) {
            $loan_product = LoanProduct::find($request->product_id);
        } else {
            if (empty($loan->product)) {
                $loan_product = LoanProduct::first();
            } else {
                $loan_product = $loan->product;
            }
        }
        if (isset($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = '';
        }
        if (empty($loan_product)) {
            Flash::warning("No loan product set. You must first set a loan product");
            return redirect()->back();
        }
        $loan_due_items = GeneralHelper::loan_due_items($loan->id);
        $paid_items = GeneralHelper::loan_paid_items($loan->id);
        if ($request->type == 1) {
            $principal = $loan_due_items["principal"] + $loan_due_items["interest"] - $paid_items['principal'] - $paid_items['interest'];
        }
        if ($request->type == 2) {
            $principal = $loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] - $paid_items['principal'] - $paid_items['interest'] - $paid_items['fees'];
        }
        if ($request->type == 3) {
            $principal = GeneralHelper::loan_total_balance($id);
        }
        if ($request->type == 0) {
            $principal = $loan_due_items["principal"] - $paid_items['principal'];
        }
        $charges = array();
        foreach (LoanProductCharge::where('loan_product_id', $loan_product->id)->get() as $key) {
            if (!empty($key->charge)) {
                $charges[$key->id] = $key->charge->name;
            }

        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        return view('loan.reschedule',
            compact('borrowers', 'loan_disbursed_by', 'loan_products', 'loan_product', 'borrower_id', 'custom_fields',
                'charges', 'principal', 'loan'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan_product = LoanProduct::find($request->loan_product_id);
        if ($request->principal > $loan_product->maximum_principal) {
            Flash::warning(trans('general.principle_greater_than_maximum') . "(" . $loan_product->maximum_principal . ")");
            return redirect()->back()->withInput();
        }
        if ($request->principal < $loan_product->minimum_principal) {
            Flash::warning(trans('general.principle_less_than_minimum') . "(" . $loan_product->minimum_principal . ")");
            return redirect()->back()->withInput();
        }
        if ($request->interest_rate > $loan_product->maximum_interest_rate) {
            Flash::warning(trans('general.interest_greater_than_maximum') . "(" . $loan_product->maximum_interest_rate . ")");
            return redirect()->back()->withInput();
        }
        if ($request->interest_rate < $loan_product->minimum_interest_rate) {
            Flash::warning(trans('general.interest_less_than_minimum') . "(" . $loan_product->minimum_interest_rate . ")");
            return redirect()->back()->withInput();
        }

        $loan = new Loan();
        $loan->principal = $request->principal;
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->branch_id = session('branch_id');
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $request->borrower_id;
        $loan->applied_amount = $request->principal;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->user_id = Sentinel::getUser()->id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        if (!empty($request->first_payment_date)) {
            $loan->first_payment_date = $request->first_payment_date;
        }
        $loan->description = $request->description;
        $files = array();
        if (!empty($request->file('files'))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "loan_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $loan->files = serialize($files);
        $loan->save();

        //save custom meta
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $loan->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }

        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $amount = "charge_amount_" . $key;
                $date = "charge_date_" . $key;
                $loan_charge = new LoanCharge();
                $loan_charge->loan_id = $loan->id;
                $loan_charge->user_id = Sentinel::getUser()->id;
                $loan_charge->charge_id = $key;
                $loan_charge->amount = $request->$amount;
                if (!empty($request->$date)) {
                    $loan_charge->date = $request->$date;
                }
                $loan_charge->save();
            }
        }


        $period = GeneralHelper::loan_period($loan->id);
        $loan = Loan::find($loan->id);
        if ($loan->repayment_cycle == 'daily') {
            $repayment_cycle = 'day';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' days')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'weekly') {
            $repayment_cycle = 'week';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' weeks')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'quarterly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'yearly') {
            $repayment_cycle = 'year';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' years')),
                'Y-m-d');
        }
        $loan->save();
        GeneralHelper::audit_trail("Added loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/data');
    }


    public function show($loan)
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }
        $guarantors = array();
        foreach (Guarantor::all() as $key) {
            $guarantors[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        foreach (LoanGuarantor::where('loan_id', $loan->id)->get() as $key) {
            $guarantors = array_except($guarantors, $key->id);
        }
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $custom_fields = CustomFieldMeta::where('category', 'loans')->where('parent_id', $loan->id)->get();
        return view('loan.show',
            compact('loan', 'schedules', 'payments', 'custom_fields', 'loan_disbursed_by', 'guarantors'));
    }

    public function approve(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'approved';
        $loan->approved_date = $request->approved_date;
        $loan->approved_notes = $request->approved_notes;
        $loan->approved_by_id = Sentinel::getUser()->id;
        $loan->approved_amount = $request->approved_amount;
        $loan->principal = $request->approved_amount;
        $loan->save();
        GeneralHelper::audit_trail("Approved loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function unapprove($id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'pending';
        $loan->save();
        GeneralHelper::audit_trail("Unapproved loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function disburse(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('loans.disburse')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if ($request->first_payment_date < $request->disbursed_date) {
            Flash::warning(trans('general.disburse_date_greater_than_first_payment'));
            return redirect()->back()->withInput();
        }
        //delete previously created schedules and payments
        LoanSchedule::where('loan_id', $loan->id)->delete();
        LoanRepayment::where('loan_id', $loan->id)->delete();
        $interest_rate = GeneralHelper::determine_interest_rate($loan->id);
        $period = GeneralHelper::loan_period($loan->id);
        $loan = Loan::find($loan->id);
        $loan_product = $loan->loan_product;
        //check if bank has the amount
        if ($loan_product->check_float == 1) {
            if (empty($loan_product->chart_fund_source)) {
                Flash::warning(trans('general.insufficient_amount_in_bank'));
                return redirect()->back();
            }
            if ((JournalEntry::where('account_id', $loan_product->chart_fund_source->id)->sum('debit') - JournalEntry::where('account_id', $loan_product->chart_fund_source->id)->sum('credit')) < $loan->principal) {
                Flash::warning(trans('general.insufficient_amount_in_bank'));
                return redirect()->back();
            }
        }
        if ($loan->repayment_cycle == 'daily') {
            $repayment_cycle = '1 days';
            $repayment_type = 'days';
        }
        if ($loan->repayment_cycle == 'weekly') {
            $repayment_cycle = '1 weeks';
            $repayment_type = 'weeks';
        }
        if ($loan->repayment_cycle == 'monthly') {
            $repayment_cycle = 'month';
            $repayment_type = 'months';
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            $repayment_cycle = '2 months';
            $repayment_type = 'months';

        }
        if ($loan->repayment_cycle == 'quarterly') {
            $repayment_cycle = '4 months';
            $repayment_type = 'months';
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            $repayment_cycle = '6 months';
            $repayment_type = 'months';
        }
        if ($loan->repayment_cycle == 'yearly') {
            $repayment_cycle = '1 years';
            $repayment_type = 'years';
        }
        if (empty($request->first_payment_date)) {
            $first_payment_date = date_format(date_add(date_create($request->disbursed_date),
                date_interval_create_from_date_string($repayment_cycle)),
                'Y-m-d');
        } else {
            $first_payment_date = $request->first_payment_date;
        }
        $loan->maturity_date = date_format(date_add(date_create($first_payment_date),
            date_interval_create_from_date_string($period . ' ' . $repayment_type)),
            'Y-m-d');
        $loan->status = 'disbursed';
        $loan->loan_disbursed_by_id = $request->loan_disbursed_by_id;
        $loan->disbursed_notes = $request->disbursed_notes;
        $loan->first_payment_date = $first_payment_date;
        $loan->disbursed_by_id = Sentinel::getUser()->id;
        $loan->disbursed_date = $request->disbursed_date;
        $loan->release_date = $request->disbursed_date;
        $date = explode('-', $request->disbursed_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        $loan->save();

        //generate schedules until period finished
        $next_payment = $first_payment_date;
        $balance = $loan->principal;
        $total_interest = 0;
        for ($i = 1; $i <= $period; $i++) {
            $loan_schedule = new LoanSchedule();
            $loan_schedule->loan_id = $loan->id;
            $loan_schedule->branch_id = session('branch_id');
            $loan_schedule->borrower_id = $loan->borrower_id;
            $loan_schedule->description = trans_choice('general.repayment', 1);
            $loan_schedule->due_date = $next_payment;
            $date = explode('-', $next_payment);
            $loan_schedule->month = $date[1];
            $loan_schedule->year = $date[0];
            //determine which method to use
            $due = 0;
            //reducing balance equal installments
            if ($loan->interest_method == 'declining_balance_equal_installments') {
                $due = GeneralHelper::amortized_monthly_payment($loan->id, $loan->principal);
                //determine if we have grace period for interest
                $interest = ($interest_rate * $balance);
                $loan_schedule->principal = ($due - $interest);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->due = $due;
                //determine next balance
                $balance = ($balance - ($due - $interest));
                $loan_schedule->principal_balance = $balance;

            }
            //reducing balance equal principle
            if ($loan->interest_method == 'declining_balance_equal_principal') {
                $principal = $loan->principal / $period;
                $loan_schedule->principal = ($principal);
                $interest = ($interest_rate * $balance);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->due = $principal + $interest;
                //determine next balance
                $balance = ($balance - ($principal + $interest));
                $loan_schedule->principal_balance = $balance;

            }
            //flat  method
            if ($loan->interest_method == 'flat_rate') {
                $principal = $loan->principal / $period;
                $interest = ($interest_rate * $loan->principal);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->principal = $principal;
                $loan_schedule->due = $principal + $interest;
                //determine next balance
                $balance = ($balance - $principal);
                $loan_schedule->principal_balance = $balance;
            }
            //interest only method
            if ($loan->interest_method == 'interest_only') {
                if ($i == $period) {
                    $principal = $loan->principal;
                } else {
                    $principal = 0;
                }
                $interest = ($interest_rate * $loan->principal);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->principal = $principal;
                $loan_schedule->due = $principal + $interest;
                //determine next balance
                $balance = ($balance - $principal);
                $loan_schedule->principal_balance = $balance;
            }
            $total_interest = $total_interest + $interest;
            //determine next due date
            if ($loan->repayment_cycle == 'daily') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 days')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'weekly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 weeks')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'monthly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'bi_monthly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('2 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'quarterly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('4 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'semi_annually') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('6 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'yearly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 years')),
                    'Y-m-d');
            }
            if ($i == $period) {
                $loan_schedule->principal_balance = round($balance);
            }
            $loan_schedule->save();
        }
        $loan = Loan::find($loan->id);
        $loan->maturity_date = $next_payment;
        $loan->save();
        $fees_disbursement = 0;
        $fees_installment = 0;
        $fees_due_date = [];
        $fees_due_date_amount = 0;
        foreach ($loan->charges as $key) {
            if (!empty($key->charge)) {
                if ($key->charge->charge_type == "disbursement") {
                    if ($key->charge->charge_option == "fixed") {
                        $fees_disbursement = $fees_disbursement + $key->amount;
                    } else {
                        if ($key->charge->charge_option == "principal_due") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "principal_interest") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($key->charge->charge_option == "interest_due") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * $total_interest) / 100;
                        }
                        if ($key->charge->charge_option == "original_principal") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "total_due") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                }
                if ($key->charge->charge_type == "installment_fee") {
                    if ($key->charge->charge_option == "fixed") {
                        $fees_installment = $fees_installment + $key->amount;
                    } else {
                        if ($key->charge->charge_option == "principal_due") {
                            $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "principal_interest") {
                            $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($key->charge->charge_option == "interest_due") {
                            $fees_installment = $fees_installment + ($key->amount * $total_interest) / 100;
                        }
                        if ($key->charge->charge_option == "original_principal") {
                            $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "total_due") {
                            $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                }
                if ($key->charge->charge_type == "specified_due_date") {
                    if ($key->charge->charge_option == "fixed") {
                        $fees_due_date_amount = $fees_due_date_amount + $key->amount;
                        $fees_due_date[$key->charge->date] = $key->amount;
                    } else {
                        if ($key->charge->charge_option == "principal_due") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "principal_interest") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($key->charge->charge_option == "interest_due") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $total_interest) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * $total_interest) / 100;
                        }
                        if ($key->charge->charge_option == "original_principal") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "total_due") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                }
            }
        }
        //add disbursal transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "disbursement";
        $loan_transaction->date = $request->disbursed_date;
        $date = explode('-', $request->disbursed_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->debit = $loan->principal;
        $loan_transaction->save();
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "interest";
        $loan_transaction->date = $request->disbursed_date;
        $date = explode('-', $request->disbursed_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->debit = $total_interest;
        $loan_transaction->save();
        //add fees transactions
        if ($fees_disbursement > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "disbursement_fee";
            $loan_transaction->date = $request->disbursed_date;
            $date = explode('-', $request->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $fees_disbursement;
            $loan_transaction->save();

            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "repayment_disbursement";
            $loan_transaction->date = $request->disbursed_date;
            $date = explode('-', $request->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $fees_disbursement;
            $loan_transaction->save();
            //add journal entry for payment and charge
            if (!empty($loan->loan_product->chart_income_fee)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_fee->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'fee';
                $journal->name = "Fee Income";
                $journal->loan_id = $loan->id;
                $journal->credit = $fees_disbursement;
                $journal->reference = $loan_transaction->id;
                $journal->save();
            }
            if (!empty($loan->loan_product->chart_fund_source)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_fund_source->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'fee';
                $journal->name = "Fee Income";
                $journal->loan_id = $loan->id;
                $journal->debit = $fees_disbursement;
                $journal->reference = $loan_transaction->id;
                $journal->save();
            }
        }
        if ($fees_installment > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "installment_fee";
            $loan_transaction->reversible = 1;
            $loan_transaction->date = $request->disbursed_date;
            $date = explode('-', $request->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $fees_installment;
            $loan_transaction->save();
            //add installment to schedules
            foreach (LoanSchedule::where('loan_id', $loan->id)->get() as $key) {
                $schedule = LoanSchedule::find($key->id);
                $schedule->fees = $fees_installment;
                $schedule->save();
            }
        }
        if ($fees_due_date_amount > 0) {
            foreach ($fees_due_date as $key => $value) {
                $due_date = GeneralHelper::determine_due_date($loan->id, $key);
                if (!empty($due_date)) {
                    $schedule = LoanSchedule::where('loan_id', $loan->id)->where('due_date', $due_date)->first();
                    $schedule->fees = $schedule->fees + $value;
                    $schedule->save();
                    $loan_transaction = new LoanTransaction();
                    $loan_transaction->user_id = Sentinel::getUser()->id;
                    $loan_transaction->branch_id = session('branch_id');
                    $loan_transaction->loan_id = $loan->id;
                    $loan_transaction->loan_schedule_id = $schedule->id;
                    $loan_transaction->reversible = 1;
                    $loan_transaction->borrower_id = $loan->borrower_id;
                    $loan_transaction->transaction_type = "specified_due_date_fee";
                    $loan_transaction->date = $due_date;
                    $date = explode('-', $due_date);
                    $loan_transaction->year = $date[0];
                    $loan_transaction->month = $date[1];
                    $loan_transaction->debit = $value;
                    $loan_transaction->save();
                }
            }

        }
        //debit and credit the necessary accounts
        if (!empty($loan->loan_product->chart_fund_source)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $loan->loan_product->chart_fund_source->id;
            $journal->branch_id = $loan->branch_id;
            $journal->date = $request->disbursed_date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $loan->borrower_id;
            $journal->transaction_type = 'disbursement';
            $journal->name = "Loan Disbursement";
            $journal->loan_id = $loan->id;
            $journal->credit = $loan->principal;
            $journal->reference = $loan->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if (!empty($loan->loan_product->chart_loan_portfolio)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
            $journal->branch_id = $loan->branch_id;
            $journal->date = $request->disbursed_date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $loan->borrower_id;
            $journal->transaction_type = 'disbursement';
            $journal->name = "Loan Disbursement";
            $journal->loan_id = $loan->id;
            $journal->debit = $loan->principal;
            $journal->reference = $loan->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if ($loan->loan_product->accounting_rule == "accrual_upfront") {
            //we need to save the accrued interest in journal here
            if (!empty($loan->loan_product->chart_receivable_interest)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'accrual';
                $journal->name = "Accrued Interest";
                $journal->loan_id = $loan->id;
                $journal->debit = $total_interest;
                $journal->reference = $loan->id;
                $journal->save();
            }
            if (!empty($loan->loan_product->chart_income_interest)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_interest->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'accrual';
                $journal->name = "Accrued Interest";
                $journal->loan_id = $loan->id;
                $journal->credit = $total_interest;
                $journal->reference = $loan->id;
                $journal->save();
            }
        }
        GeneralHelper::audit_trail("Disbursed loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $loan->id . '/show');
    }

    public function rescheduleStore(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.reschedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $l = Loan::find($id);
        $l->status = 'rescheduled';
        $l->save();
        $loan = new Loan();
        $loan->principal = $request->principal;
        $loan->status = "disbursed";
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->branch_id = session('branch_id');
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $l->borrower_id;
        $loan->applied_amount = $request->principal;
        $loan->user_id = Sentinel::getUser()->id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        if (!empty($request->first_payment_date)) {
            $loan->first_payment_date = $request->first_payment_date;
        }
        $loan->description = $request->description;
        $files = array();
        if (!empty($request->file('files'))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $files[$count] = $key->getClientOriginalName();
                    $key->move(public_path() . '/uploads',
                        $key->getClientOriginalName());
                }
                $count++;
            }
        }
        $loan->files = serialize($files);
        $loan->save();
        $interest_rate = GeneralHelper::determine_interest_rate($loan->id);
        $period = GeneralHelper::loan_period($loan->id);
        //save custom meta
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $loan->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }
        if ($loan->repayment_cycle == 'daily') {
            $repayment_cycle = '1 days';
            $repayment_type = 'days';
        }
        if ($loan->repayment_cycle == 'weekly') {
            $repayment_cycle = '1 weeks';
            $repayment_type = 'weeks';
        }
        if ($loan->repayment_cycle == 'monthly') {
            $repayment_cycle = 'month';
            $repayment_type = 'months';
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            $repayment_cycle = '2 months';
            $repayment_type = 'months';

        }
        if ($loan->repayment_cycle == 'quarterly') {
            $repayment_cycle = '4 months';
            $repayment_type = 'months';
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            $repayment_cycle = '6 months';
            $repayment_type = 'months';
        }
        if ($loan->repayment_cycle == 'yearly') {
            $repayment_cycle = '1 years';
            $repayment_type = 'years';
        }
        if (empty($request->first_payment_date)) {
            $first_payment_date = date_format(date_add(date_create($request->disbursed_date),
                date_interval_create_from_date_string($repayment_cycle)),
                'Y-m-d');
        } else {
            $first_payment_date = $request->first_payment_date;
        }
        $loan->maturity_date = date_format(date_add(date_create($first_payment_date),
            date_interval_create_from_date_string($period . ' ' . $repayment_type)),
            'Y-m-d');
        $loan->status = 'disbursed';
        $loan->loan_disbursed_by_id = $request->loan_disbursed_by_id;
        $loan->disbursed_notes = $request->disbursed_notes;
        $loan->first_payment_date = $first_payment_date;
        $loan->disbursed_by_id = Sentinel::getUser()->id;
        $loan->disbursed_date = $request->disbursed_date;
        $loan->release_date = $request->disbursed_date;
        $date = explode('-', $request->disbursed_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        $loan->save();

        //generate schedules until period finished
        $next_payment = $first_payment_date;
        $balance = $loan->principal;
        $total_interest = 0;
        for ($i = 1; $i <= $period; $i++) {
            $loan_schedule = new LoanSchedule();
            $loan_schedule->loan_id = $loan->id;
            $loan_schedule->branch_id = session('branch_id');
            $loan_schedule->borrower_id = $loan->borrower_id;
            $loan_schedule->description = trans_choice('general.repayment', 1);
            $loan_schedule->due_date = $next_payment;
            $date = explode('-', $next_payment);
            $loan_schedule->month = $date[1];
            $loan_schedule->year = $date[0];
            //determine which method to use
            $due = 0;
            //reducing balance equal installments
            if ($loan->interest_method == 'declining_balance_equal_installments') {
                $due = GeneralHelper::amortized_monthly_payment($loan->id, $loan->principal);
                //determine if we have grace period for interest
                $interest = ($interest_rate * $balance);
                $loan_schedule->principal = ($due - $interest);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->due = $due;
                //determine next balance
                $balance = ($balance - ($due - $interest));
                $loan_schedule->principal_balance = $balance;

            }
            //reducing balance equal principle
            if ($loan->interest_method == 'declining_balance_equal_principal') {
                $principal = $loan->principal / $period;
                $loan_schedule->principal = ($principal);
                $interest = ($interest_rate * $balance);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->due = $principal + $interest;
                //determine next balance
                $balance = ($balance - ($principal + $interest));
                $loan_schedule->principal_balance = $balance;

            }
            //flat  method
            if ($loan->interest_method == 'flat_rate') {
                $principal = $loan->principal / $period;
                $interest = ($interest_rate * $loan->principal);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->principal = $principal;
                $loan_schedule->due = $principal + $interest;
                //determine next balance
                $balance = ($balance - $principal);
                $loan_schedule->principal_balance = $balance;
            }
            //interest only method
            if ($loan->interest_method == 'interest_only') {
                if ($i == $period) {
                    $principal = $loan->principal;
                } else {
                    $principal = 0;
                }
                $interest = ($interest_rate * $loan->principal);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_schedule->interest = 0;
                } else {
                    $loan_schedule->interest = $interest;
                }
                $loan_schedule->principal = $principal;
                $loan_schedule->due = $principal + $interest;
                //determine next balance
                $balance = ($balance - $principal);
                $loan_schedule->principal_balance = $balance;
            }
            $total_interest = $total_interest + $interest;
            //determine next due date
            if ($loan->repayment_cycle == 'daily') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 days')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'weekly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 weeks')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'monthly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'bi_monthly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('2 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'quarterly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('4 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'semi_annually') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('6 months')),
                    'Y-m-d');
            }
            if ($loan->repayment_cycle == 'yearly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 years')),
                    'Y-m-d');
            }
            if ($i == $period) {
                $loan_schedule->principal_balance = round($balance);
            }
            $loan_schedule->save();
        }
        $loan = Loan::find($loan->id);
        $loan->maturity_date = $next_payment;
        $loan->save();
        $fees_disbursement = 0;
        $fees_installment = 0;
        $fees_due_date = [];
        $fees_due_date_amount = 0;
        foreach ($loan->charges as $key) {
            if (!empty($key->charge)) {
                if ($key->charge->charge_type == "disbursement") {
                    if ($key->charge->charge_option == "fixed") {
                        $fees_disbursement = $fees_disbursement + $key->amount;
                    } else {
                        if ($key->charge->charge_option == "principal_due") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "principal_interest") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($key->charge->charge_option == "interest_due") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * $total_interest) / 100;
                        }
                        if ($key->charge->charge_option == "original_principal") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "total_due") {
                            $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                }
                if ($key->charge->charge_type == "installment_fee") {
                    if ($key->charge->charge_option == "fixed") {
                        $fees_installment = $fees_installment + $key->amount;
                    } else {
                        if ($key->charge->charge_option == "principal_due") {
                            $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "principal_interest") {
                            $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($key->charge->charge_option == "interest_due") {
                            $fees_installment = $fees_installment + ($key->amount * $total_interest) / 100;
                        }
                        if ($key->charge->charge_option == "original_principal") {
                            $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "total_due") {
                            $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                }
                if ($key->charge->charge_type == "specified_due_date") {
                    if ($key->charge->charge_option == "fixed") {
                        $fees_due_date_amount = $fees_due_date_amount + $key->amount;
                        $fees_due_date[$key->charge->date] = $key->amount;
                    } else {
                        if ($key->charge->charge_option == "principal_due") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "principal_interest") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($key->charge->charge_option == "interest_due") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $total_interest) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * $total_interest) / 100;
                        }
                        if ($key->charge->charge_option == "original_principal") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * $loan->principal) / 100;
                        }
                        if ($key->charge->charge_option == "total_due") {
                            $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            $fees_due_date[$key->charge->date] = ($key->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                }
            }
        }
        //add disbursal transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "disbursement";
        $loan_transaction->date = $request->disbursed_date;
        $date = explode('-', $request->disbursed_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->debit = $loan->principal;
        $loan_transaction->save();
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "interest";
        $loan_transaction->date = $request->disbursed_date;
        $date = explode('-', $request->disbursed_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->debit = $total_interest;
        $loan_transaction->save();
        //add fees transactions
        if ($fees_disbursement > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "disbursement_fee";
            $loan_transaction->date = $request->disbursed_date;
            $date = explode('-', $request->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $fees_disbursement;
            $loan_transaction->save();

            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "repayment_disbursement";
            $loan_transaction->date = $request->disbursed_date;
            $date = explode('-', $request->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $fees_disbursement;
            $loan_transaction->save();
            //add journal entry for payment and charge
            if (!empty($loan->loan_product->chart_income_fee)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_fee->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'fee';
                $journal->name = "Fee Income";
                $journal->loan_id = $loan->id;
                $journal->credit = $fees_disbursement;
                $journal->reference = $loan_transaction->id;
                $journal->save();
            }
            if (!empty($loan->loan_product->chart_fund_source)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_fund_source->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'fee';
                $journal->name = "Fee Income";
                $journal->loan_id = $loan->id;
                $journal->debit = $fees_disbursement;
                $journal->reference = $loan_transaction->id;
                $journal->save();
            }
        }
        if ($fees_installment > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "installment_fee";
            $loan_transaction->reversible = 1;
            $loan_transaction->date = $request->disbursed_date;
            $date = explode('-', $request->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $fees_installment;
            $loan_transaction->save();
            //add installment to schedules
            foreach (LoanSchedule::where('loan_id', $loan->id)->get() as $key) {
                $schedule = LoanSchedule::find($key->id);
                $schedule->fees = $fees_installment;
                $schedule->save();
            }
        }
        if ($fees_due_date_amount > 0) {
            foreach ($fees_due_date as $key => $value) {
                $due_date = GeneralHelper::determine_due_date($loan->id, $key);
                if (!empty($due_date)) {
                    $schedule = LoanSchedule::where('loan_id', $loan->id)->where('due_date', $due_date)->first();
                    $schedule->fees = $schedule->fees + $value;
                    $schedule->save();
                    $loan_transaction = new LoanTransaction();
                    $loan_transaction->user_id = Sentinel::getUser()->id;
                    $loan_transaction->branch_id = session('branch_id');
                    $loan_transaction->loan_id = $loan->id;
                    $loan_transaction->loan_schedule_id = $schedule->id;
                    $loan_transaction->reversible = 1;
                    $loan_transaction->borrower_id = $loan->borrower_id;
                    $loan_transaction->transaction_type = "specified_due_date_fee";
                    $loan_transaction->date = $due_date;
                    $date = explode('-', $due_date);
                    $loan_transaction->year = $date[0];
                    $loan_transaction->month = $date[1];
                    $loan_transaction->debit = $value;
                    $loan_transaction->save();
                }
            }

        }
        //debit and credit the necessary accounts
        if (!empty($loan->loan_product->chart_fund_source)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $loan->loan_product->chart_fund_source->id;
            $journal->branch_id = $loan->branch_id;
            $journal->date = $request->disbursed_date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $loan->borrower_id;
            $journal->transaction_type = 'disbursement';
            $journal->name = "Loan Disbursement";
            $journal->loan_id = $loan->id;
            $journal->credit = $loan->principal;
            $journal->reference = $loan->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if (!empty($loan->loan_product->chart_loan_portfolio)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
            $journal->branch_id = $loan->branch_id;
            $journal->date = $request->disbursed_date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $loan->borrower_id;
            $journal->transaction_type = 'disbursement';
            $journal->name = "Loan Disbursement";
            $journal->loan_id = $loan->id;
            $journal->debit = $loan->principal;
            $journal->reference = $loan->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if ($loan->loan_product->accounting_rule == "accrual_upfront") {
            //we need to save the accrued interest in journal here
            if (!empty($loan->loan_product->chart_receivable_interest)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'accrual';
                $journal->name = "Accrued Interest";
                $journal->loan_id = $loan->id;
                $journal->debit = $total_interest;
                $journal->reference = $loan->id;
                $journal->save();
            }
            if (!empty($loan->loan_product->chart_income_interest)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_interest->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->disbursed_date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $loan->borrower_id;
                $journal->transaction_type = 'accrual';
                $journal->name = "Accrued Interest";
                $journal->loan_id = $loan->id;
                $journal->credit = $total_interest;
                $journal->reference = $loan->id;
                $journal->save();
            }
        }
        GeneralHelper::audit_trail("Rescheduled loan with id:" . $l->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undisburse($id)
    {
        if (!Sentinel::hasAccess('loans.disburse')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        //delete previously created schedules and payments
        LoanSchedule::where('loan_id', $id)->delete();
        LoanRepayment::where('loan_id', $id)->delete();
        Capital::where('loan_id', $id)->delete();
        $loan = Loan::find($id);
        $loan->status = 'approved';
        $loan->save();
        //lets reverse all transactions that have been made for this loan
        foreach (LoanTransaction::where('loan_id', $id)->where('reversed', 0)->get() as $key) {
            $loan_transaction = LoanTransaction::find($key->id);
            if ($key->debit > $key->credit) {
                $loan_transaction->credit = $loan_transaction->debit;
            } else {
                $loan_transaction->debit = $loan_transaction->credit;
            }
            $loan_transaction->reversible = 0;
            $loan_transaction->reversed = 1;
            $loan_transaction->reversal_type = 'system';
            $loan_transaction->save();
            //reverse journal transactions
            foreach (JournalEntry::where('reference', $key->id)->where('loan_id',
                $id)->get() as $k) {
                $journal = JournalEntry::find($k->id);
                if ($k->debit > $k->credit) {
                    $journal->credit = $journal->debit;
                } else {
                    $journal->debit = $journal->credit;
                }
                $journal->save();
            }
        }
        GeneralHelper::audit_trail("Undisbursed loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $loan->id . '/show');
    }

    public function decline(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'declined';
        $loan->declined_date = $request->declined_date;
        $loan->declined_notes = $request->declined_notes;
        $loan->declined_by_id = Sentinel::getUser()->id;
        $loan->save();
        GeneralHelper::audit_trail("Declined loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function write_off(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.writeoff')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'written_off';
        $loan->written_off_date = $request->written_off_date;
        $loan->written_off_notes = $request->written_off_notes;
        $loan->written_off_by_id = Sentinel::getUser()->id;
        $loan->save();
        $amount = GeneralHelper::loan_due_items($loan->id)["principal"] - GeneralHelper::loan_paid_items($loan->id)["principal"];
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "write_off";
        $loan_transaction->date = $request->written_off_date;
        $loan_transaction->reversible = 1;
        $date = explode('-', $request->written_off_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = GeneralHelper::loan_total_balance($loan->id);
        $loan_transaction->notes = $request->written_off_notes;
        $loan_transaction->save();
        //fire payment added event
        //debit and credit the necessary accounts

        //return $allocation;
        //principal

        if (!empty($loan->loan_product->chart_loan_portfolio)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
            $journal->branch_id = $loan->branch_id;
            $journal->date = $request->collection_date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $loan->borrower_id;
            $journal->transaction_type = 'close_write_off';
            $journal->name = "Loan Written Off";
            $journal->loan_id = $loan->id;
            $journal->loan_repayment_id = $loan_transaction->id;
            $journal->credit = $amount;
            $journal->reference = $loan_transaction->id;
            $journal->save();
        }
        if (!empty($loan->loan_product->chart_loans_written_off)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $loan->loan_product->chart_fund_source->id;
            $journal->branch_id = $loan->branch_id;
            $journal->date = $request->collection_date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $loan->borrower_id;
            $journal->transaction_type = 'close_write_off';
            $journal->name = "Loan Written Off";
            $journal->loan_id = $loan->id;
            $journal->loan_transaction_id = $loan_transaction->id;
            $journal->debit = $amount;
            $journal->reference = $loan_transaction->id;
            $journal->save();
        }
        GeneralHelper::audit_trail("Write off loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function withdraw(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.withdraw')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'withdrawn';
        $loan->withdrawn_date = $request->withdrawn_date;
        $loan->withdrawn_notes = $request->withdrawn_notes;
        $loan->withdrawn_by_id = Sentinel::getUser()->id;
        $loan->save();
        GeneralHelper::audit_trail("Withdraw loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function unwithdraw($id)
    {
        if (!Sentinel::hasAccess('loans.withdraw')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'disbursed';
        $loan->save();
        GeneralHelper::audit_trail("Unwithdraw loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function unwrite_off($id)
    {
        if (!Sentinel::hasAccess('loans.writeoff')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->status = 'disbursed';
        $loan->save();
        GeneralHelper::audit_trail("Unwriteoff loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $id . '/show');
    }

    public function edit($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        $users = [];
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }

        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }
        $loan_overdue_penalties = array();
        foreach (LoanOverduePenalty::all() as $key) {
            $loan_overdue_penalties[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        $loan_fees = LoanFee::all();
        return view('loan.edit',
            compact('loan', 'borrowers', 'loan_disbursed_by', 'loan_products', 'custom_fields', 'loan_fees',
                'loan_overdue_penalties', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $loan->principal = $request->principal;
        $loan->applied_amount = $request->principal;
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $request->borrower_id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        if (!empty($request->first_payment_date)) {
            $loan->first_payment_date = $request->first_payment_date;
        }

        $loan->description = $request->description;
        $files = unserialize($loan->files);
        $count = count($files);
        if (!empty($request->file('files'))) {
            foreach ($request->file('files') as $key) {
                $count++;
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "loan_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $loan->files = serialize($files);
        $loan->save();
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'loans')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'loans')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }
        LoanCharge::where('loan_id', $loan->id)->delete();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $amount = "charge_amount_" . $key;
                $date = "charge_date_" . $key;
                $loan_charge = new LoanCharge();
                $loan_charge->loan_id = $loan->id;
                $loan_charge->user_id = Sentinel::getUser()->id;
                $loan_charge->charge_id = $key;
                $loan_charge->amount = $request->$amount;
                if (!empty($request->$date)) {
                    $loan_charge->date = $request->$date;
                }
                $loan_charge->save();
            }
        }
        GeneralHelper::audit_trail("Updated loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        Loan::destroy($id);
        LoanSchedule::where('loan_id', $id)->delete();
        LoanRepayment::where('loan_id', $id)->delete();
        Guarantor::where('loan_id', $id)->delete();
        LoanCharge::where('loan_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted loan with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/data');
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $files = unserialize($loan->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $loan->files = serialize($files);
        $loan->save();


    }

    public function waiveInterest(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $interest = GeneralHelper::loan_due_items($loan->id);
        $amount = $interest["interest"] - $interest["interest_paid"];
        if ($request->amount > round($amount, 2)) {
            Flash::warning("Amount is more than the total interest(" . $amount . ')');
            return redirect()->back()->withInput();

        }
        if ($request->date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();
        }
        if ($request->date < $loan->disbursed_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();
        }
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "waiver";
        $loan_transaction->date = $request->date;
        $loan_transaction->reversible = 0;
        $date = explode('-', $request->date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->amount;
        $loan_transaction->notes = $request->notes;
        $loan_transaction->save();
        event(new InterestWaived($loan_transaction));
        GeneralHelper::audit_trail("Waived interest for loan with id " . $loan->id);
        Flash::success("Repayment successfully saved");
        return redirect()->back();
    }

    public function indexRepayment()
    {
        if (!Sentinel::hasAccess('repayments')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }


        return view('loan_repayment.data', compact('data'));
    }

    public function get_transactions(Request $request)
    {
        if (!Sentinel::hasAccess('repayments')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
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

        $query = DB::table("loan_transactions")->leftJoin("borrowers", "borrowers.id", "loan_transactions.borrower_id")->leftJoin("users", "users.id", "loan_transactions.user_id")->leftJoin("loan_repayment_methods", "loan_repayment_methods.id", "loan_transactions.repayment_method_id")->selectRaw(DB::raw("borrowers.first_name borrower_first_name,borrowers.last_name borrower_last_name,users.first_name user_first_name,users.last_name user_last_name,loan_repayment_methods.name repayment_method,loan_transactions.*"))->where('loan_transactions.branch_id', session('branch_id'))->when($transaction_type, function ($query) use ($transaction_type) {
            $query->where("loan_transactions.transaction_type", $transaction_type);
        })->when($borrower_id, function ($query) use ($borrower_id) {
            $query->where("loan_transactions.borrower_id", $borrower_id);
        })->when($reversed, function ($query) use ($reversed) {
            $query->where("loan_transactions.reversed", $reversed);
        });
        return DataTables::of($query)->editColumn('borrower', function ($data) {
            return '<a href="' . url('borrower/' . $data->borrower_id . '/show') . '">' . $data->borrower_first_name . ' ' . $data->borrower_last_name . '</a>';
        })->editColumn('collected_by', function ($data) {
            return '<a href="' . url('user/' . $data->user_id . '/show') . '">' . $data->user_first_name . ' ' . $data->user_last_name . '</a>';
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
            if (Sentinel::hasAccess('loans.view')) {
                $action .= '<li><a href="' . url('loan/transaction/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            }
            if ($data->transaction_type == 'repayment' && $data->reversible == 1) {
                $action .= '<li><a href="' . url('loan/transaction/' . $data->id . '/print') . '" class="" target="_blank">' . trans_choice('general.print', 2) . ' ' . trans_choice('general.receipt', 1) . '</a></li>';
                $action .= '<li><a href="' . url('loan/transaction/' . $data->id . '/pdf') . '" class="" target="_blank">' . trans_choice('general.pdf', 2) . ' ' . trans_choice('general.receipt', 1) . '</a></li>';
                $action .= '<li><a href="' . url('loan/repayment/' . $data->id . '/edit') . '" class="">' . trans_choice('general.edit', 2) . '</a></li>';
                $action .= '<li><a href="' . url('loan/repayment/' . $data->id . '/reverse') . '" class="delete">' . trans_choice('general.reverse', 2) . '</a></li>';

            }
            if (($data->transaction_type == 'penalty' || $data->transaction_type == 'installment_fee' || $data->transaction_type == 'specified_due_date_fee') && $data->reversible == 1) {
                $action .= '<li><a href="' . url('loan/transaction/' . $data->id . '/waive') . '" class="delete">' . trans_choice('general.waive', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/transaction/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->editColumn('loan_id', function ($data) {
            return '<a href="' . url('loan/' . $data->loan_id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'loan_id', 'borrower', 'collected_by', 'action'])->make(true);
    }

//loan repayments
    public function createBulkRepayment()
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loans = array();
        foreach (Loan::all() as $key) {
            $loans[$key->id] = $key->borrower->first_name . ' ' . $key->borrower->last_name . '(' . trans_choice('general.loan',
                    1) . '#' . $key->id . ',' . trans_choice('general.due',
                    1) . ':' . GeneralHelper::loan_total_balance($key->id) . ')';
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }
        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.bulk', compact('loan', 'repayment_methods', 'custom_fields', 'loans'));
    }

    public function storeBulkRepayment(Request $request)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        for ($i = 1; $i <= 20; $i++) {
            $amount = "repayment_amount" . $i;
            $loan_id = "loan_id" . $i;
            $repayment_method = "repayment_method_id" . $i;
            $receipt = "receipt" . $i;
            $collected_date = "repayment_collected_date" . $i;
            $repayment_description = "repayment_description" . $i;
            if (!empty($request->$amount && !empty($request->$loan_id))) {
                $loan = Loan::find($request->$loan_id);
                if ($request->$amount > round(GeneralHelper::loan_total_balance($loan->id), 2)) {
                    Flash::warning("Amount is more than the balance(" . GeneralHelper::loan_total_balance($loan->id) . ')');
                    return redirect()->back()->withInput();

                } else {
                    $repayment = new LoanRepayment();
                    $repayment->user_id = Sentinel::getUser()->id;
                    $repayment->amount = $request->$amount;
                    $repayment->loan_id = $loan->id;
                    $repayment->borrower_id = $loan->borrower_id;
                    $repayment->branch_id = session('branch_id');
                    $repayment->collection_date = $request->$collected_date;
                    $repayment->receipt = $request->$receipt;
                    $repayment->repayment_method_id = $request->$repayment_method;
                    $repayment->notes = $request->$repayment_description;
                    $date = explode('-', $request->$collected_date);
                    $repayment->year = $date[0];
                    $repayment->month = $date[1];
                    //determine which schedule due date the payment applies too
                    $schedule = LoanSchedule::where('due_date', '>=', $request->$collected_date)->where('loan_id',
                        $loan->id)->orderBy('due_date',
                        'asc')->first();
                    if (!empty($schedule)) {
                        $repayment->due_date = $schedule->due_date;
                    } else {
                        $schedule = LoanSchedule::where('loan_id',
                            $loan->id)->orderBy('due_date',
                            'desc')->first();
                        if ($request->$collected_date > $schedule->due_date) {
                            $repayment->due_date = $schedule->due_date;
                        } else {
                            $schedule = LoanSchedule::where('due_date', '>',
                                $request->$collected_date)->where('loan_id',
                                $loan->id)->orderBy('due_date',
                                'asc')->first();
                            $repayment->due_date = $schedule->due_date;
                        }

                    }
                    $repayment->save();

                    //update loan status if need be
                    if (round(GeneralHelper::loan_total_balance($loan->id), 2) == 0) {
                        $l = Loan::find($loan->id);
                        $l->status = "closed";
                        $l->save();

                    }
                    //check if late repayment is to be applied when adding payment


                }
            }
            //notify borrower


        }
        GeneralHelper::audit_trail("Added  bulk repayment");
        Flash::success("Repayment successfully saved");
        return redirect('repayment/data');
    }

    public function create_group_repayment($id)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        $repayment_methods = LoanRepaymentMethod::all();

        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.group', compact('id', 'repayment_methods', 'custom_fields', 'loans'));
    }


    public function store_group_repayment(Request $request, $id)
    {


        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        foreach (DB::table('borrower_group_members')->join("borrowers", "borrowers.id", "borrower_group_members.borrower_id")->selectRaw("borrowers.first_name,borrowers.last_name,borrowers.id,borrowers.unique_number")->where("borrower_group_members.borrower_group_id", $id)->get() as $key) {
            $amount = $request->amount[$key->id];
            $loan_id = $request->loan_id[$key->id];
            $date = $request->date[$key->id];
            $receipt = $request->receipt[$key->id];
            $repayment_method_id = $request->repayment_method_id[$key->id];
            if (!empty($amount)) {
                //add interest transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->user_id = Sentinel::getUser()->id;
                $loan_transaction->branch_id = session('branch_id');
                $loan_transaction->loan_id = $loan_id;
                $loan_transaction->borrower_id = $key->id;
                $loan_transaction->transaction_type = "repayment";
                $loan_transaction->receipt = $receipt;
                $loan_transaction->date = $date;
                $loan_transaction->reversible = 1;
                $loan_transaction->repayment_method_id = $repayment_method_id;
                $date = explode('-', $date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->credit = $amount;
                $loan_transaction->save();
                //update loan status if need be
                if (round(GeneralHelper::loan_total_balance($loan_id)) <= 0) {
                    $l = Loan::find($loan_id);
                    $l->status = "closed";
                    $l->save();
                }
                event(new RepaymentCreated($loan_transaction));
            }
        }

        GeneralHelper::audit_trail("Added repayment for group with id:" . $id);
        Flash::success("Repayment successfully saved");
        return redirect('borrower/group/' . $id . '/show');

    }

    public function addRepayment()
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loans = array();
        foreach (Loan::whereIn('status',
            ['disbursed', 'closed', 'written_off'])->get() as $key) {

            if (!empty($key->borrower)) {
                $borrower = ' (' . $key->borrower->first_name . ' ' . $key->borrower->last_name . ")";
            } else {
                $borrower = '';
            }
            $loans[$key->id] = "#" . $key->id . $borrower;
        }

        return view('loan_repayment.add', compact('loans'));
    }

    public function createRepayment($loan)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }

        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.create', compact('loan', 'repayment_methods', 'custom_fields'));
    }

    public function storeRepayment(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
//return GeneralHelper::loan_allocate_payment($loan->id, $request->amount)["penalty"];

        if ($request->amount > round(GeneralHelper::loan_total_balance($loan->id), 2)) {
            Flash::warning("Amount is more than the balance(" . GeneralHelper::loan_total_balance($loan->id) . ')');
            return redirect()->back()->withInput();

        }
        if ($request->collection_date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();

        }
        if ($request->collection_date < $loan->disbursed_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();

        }
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "repayment";
        $loan_transaction->receipt = $request->receipt;
        $loan_transaction->date = $request->collection_date;
        $loan_transaction->reversible = 1;
        $loan_transaction->repayment_method_id = $request->repayment_method_id;
        $date = explode('-', $request->collection_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->amount;
        $loan_transaction->notes = $request->notes;
        $loan_transaction->save();

        //save custom meta
        $custom_fields = CustomField::where('category', 'repayments')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $loan_transaction->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "repayments";
            $custom_field->save();
        }
        //update loan status if need be
        if (round(GeneralHelper::loan_total_balance($loan->id)) <= 0) {
            $l = Loan::find($loan->id);
            $l->status = "closed";
            $l->save();
        }
        event(new RepaymentCreated($loan_transaction));
        GeneralHelper::audit_trail("Added repayment for loan with id:" . $loan->id);
        Flash::success("Repayment successfully saved");
        return redirect('loan/' . $loan->id . '/show');

    }

    public function reverseRepayment($id)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan_transaction = LoanTransaction::find($id);
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->save();
        //reverse journal transactions
        foreach (JournalEntry::where('reference', $id)->where('loan_id',
            $loan_transaction->loan_id)->where('transaction_type', 'repayment')->get() as $key) {
            $journal = JournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        //trigger transactions refresh
        event(new RepaymentReversed($loan_transaction));
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $loan_transaction->loan_id . '/show');
    }

    public function deleteRepayment($loan, $id)
    {
        if (!Sentinel::hasAccess('repayments.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        LoanRepayment::destroy($id);
        if (GeneralHelper::loan_total_balance($loan->id) > 0 && $loan->status == "closed") {
            $l = Loan::find($loan->id);
            $l->status = "disbursed";
            $l->save();
        }
        //remove entries from the journal table
        JournalEntry::where('loan_transaction_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted repayment for loan with id:" . $loan->id);
        Flash::success("Repayment successfully deleted");
        return redirect('loan/' . $loan->id . '/show');
    }

//    print repayment
    public function pdfRepayment($loan_transaction)
    {
        PDF::AddPage();
        PDF::writeHTML(View::make('loan_repayment.pdf', compact('loan_transaction'))->render());
        PDF::SetAuthor('Tererai Mugova');
        PDF::Output($loan_transaction->borrower->title . ' ' . $loan_transaction->borrower->first_name . ' ' . $loan_transaction->borrower->last_name . " - Repayment Receipt.pdf",
            'D');
    }

    public function printRepayment($loan_transaction)
    {

        return view('loan_repayment.print', compact('loan_transaction'));
    }

    public function editRepayment($loan_transaction)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }
        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.edit', compact('loan_transaction', 'repayment_methods', 'custom_fields'));
    }

    public function updateRepayment(Request $request, $id)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        $loan_transaction = LoanTransaction::find($id);
        if ($request->collection_date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();

        }
        if ($request->collection_date < $loan_transaction->loan->first_payment_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();

        }
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->save();
        //reverse journal transactions
        foreach (JournalEntry::where('loan_id',
            $loan_transaction->loan_id)->where('transaction_type', 'repayment')->get() as $key) {
            $journal = JournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->save();
        }
        //trigger transactions refresh
        //save new loan transaction

        //save custom meta
        $custom_fields = CustomField::where('category', 'repayments')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                $id)->where('category', 'repayments')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'repayments')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "repayments";
            $custom_field->save();
        }
        $loan = $loan_transaction->loan;
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "repayment";
        $loan_transaction->receipt = $request->receipt;
        $loan_transaction->date = $request->collection_date;
        $loan_transaction->reversible = 1;
        $loan_transaction->repayment_method_id = $request->repayment_method_id;
        $date = explode('-', $request->collection_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->amount;
        $loan_transaction->notes = $request->notes;
        $loan_transaction->save();
        //fire payment added event
        //debit and credit the necessary accounts
        event(new RepaymentUpdated($loan_transaction));

        //update loan status if need be
        if (round(GeneralHelper::loan_total_balance($loan_transaction->loan_id)) <= 0) {
            $l = Loan::find($loan_transaction->loan_id);
            $l->status = "closed";
            $l->save();

        } else {
            $l = Loan::find($loan_transaction->loan_id);
            $l->status = "disbursed";
            $l->save();
        }
        GeneralHelper::audit_trail("Updated repayment for loan with id:" . $loan_transaction->loan_id);
        Flash::success("Repayment successfully saved");
        return redirect('loan/' . $loan_transaction->loan_id . '/show');

    }

    //transactions
    public function showTransaction($loan_transaction)
    {
        $custom_fields = CustomFieldMeta::where('category', 'repayments')->where('parent_id',
            $loan_transaction->id)->get();
        return view('loan_transaction.show', compact('loan_transaction', 'custom_fields'));
    }

    public function pdfTransaction($loan_transaction)
    {
        $pdf = PDF::loadView('loan_transaction.pdf', compact('loan_transaction'));
        return $pdf->download($loan_transaction->borrower->title . ' ' . $loan_transaction->borrower->first_name . ' ' . $loan_transaction->borrower->last_name . " - Repayment Receipt.pdf");
    }

    public function printTransaction($loan_transaction)
    {

        return view('loan_transaction.print', compact('loan_transaction'));
    }

//edit loan schedule
    public function editSchedule($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $rows = 0;
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        return view('loan.edit_schedule', compact('loan', 'schedules', 'rows'));
    }

    public function updateSchedule(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if ($request->submit == 'add_row') {
            $rows = $request->addrows;
            $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
            return view('loan.edit_schedule', compact('loan', 'schedules', 'rows'));
        }
        if ($request->submit == 'submit') {
            //lets delete existing schedules
            LoanSchedule::where('loan_id', $loan->id)->delete();
            for ($count = 0; $count < $request->count; $count++) {
                $schedule = new LoanSchedule();
                if (empty($request->due_date) && empty($request->principal) && empty($request->interest) && empty($request->fees) && empty($request->penalty)) {
                    //do nothing
                } elseif (empty($request->due_date)) {
                    //do nothing
                } else {
                    //all rosy, lets save our data here
                    $schedule->due_date = $request->due_date[$count];
                    $schedule->principal = $request->principal[$count];
                    $schedule->description = $request->description[$count];
                    $schedule->loan_id = $loan->id;
                    $schedule->borrower_id = $loan->borrower_id;
                    $schedule->interest = $request->interest[$count];
                    $schedule->interest_waived = $request->interest_waived[$count];
                    $schedule->fees = $request->fees[$count];
                    $schedule->penalty = $request->penalty[$count];
                    $date = explode('-', $request->due_date[$count]);
                    $schedule->month = $date[1];
                    $schedule->year = $date[0];
                    $schedule->save();
                }
            }
            event(new UpdateLoanTransactions($loan));
            GeneralHelper::audit_trail("Updated Schedule for loan with id:" . $loan->id);
            Flash::success("Schedule successfully updated");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    //charges
    public function addCharge(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if ($request->date < $loan->disbursed_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();
        }
        $due_date = GeneralHelper::determine_due_date($loan->id, $request->date);
        $schedule = LoanSchedule::where('due_date', $due_date)->where('loan_id', $loan->id)->first();
        if (!empty($schedule)) {
            //add charge transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = Sentinel::getUser()->id;
            $loan_transaction->branch_id = session('branch_id');
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "specified_due_date_fee";
            $loan_transaction->date = $due_date;
            $date = explode('-', $due_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $request->amount;
            $loan_transaction->notes = $request->notes;
            $loan_transaction->reversible = 1;
            $loan_transaction->save();
            //update schedule
            $schedule->fees = $schedule->fees + $request->amount;
            $schedule->save();
            GeneralHelper::audit_trail("Added charge for loan with id " . $loan->id);
            //trigger transactions refresh
            event(new LoanTransactionUpdated($loan_transaction));
            Flash::success("Charge successfully saved");
            return redirect()->back();
        } else {
            Flash::warning("An error occurred. No schedule found to apply");
            return redirect()->back()->withInput();
        }
    }

    public function waiveTransaction(Request $request, $id)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan_transaction = LoanTransaction::find($id);
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->credit = $loan_transaction->debit;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->save();
        $schedule = LoanSchedule::where('due_date', $loan_transaction->date)->where('loan_id',
            $loan_transaction->loan_id)->first();
        if (!empty($schedule)) {
            //update schedule
            $schedule->fees = $schedule->fees - $loan_transaction->debit;
            $schedule->save();
            GeneralHelper::audit_trail("Waived charge for loan with id " . $loan_transaction->loan_id);
            //trigger transactions refresh
            event(new InterestWaived($loan_transaction));
            event(new RepaymentReversed($loan_transaction));
            Flash::success("Charge successfully saved");
            return redirect()->back();
        } else {
            Flash::warning("An error occurred. No schedule found to apply");
            return redirect()->back()->withInput();
        }
    }

    public function pdfSchedule($loan)
    {

        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $pdf = PDF::loadView('loan.pdf_schedule', compact('loan', 'schedules'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Repayment Schedule.pdf");

    }

    public function printSchedule($loan)
    {
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        return view('loan.print_schedule', compact('loan', 'schedules'));
    }

    public function pdfLoanStatement($loan)
    {
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        $pdf = PDF::loadView('loan.pdf_loan_statement', compact('loan', 'payments'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Statement.pdf");

    }

    public function printLoanStatement($loan)
    {
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        return view('loan.print_loan_statement', compact('loan', 'payments'));
    }

    public function pdfBorrowerStatement($borrower)
    {
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        $pdf = PDF::loadView('loan.pdf_borrower_statement', compact('loans'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Client Statement.pdf");

    }

    public function printBorrowerStatement($borrower)
    {
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        return view('loan.print_borrower_statement', compact('loans'));
    }

    public function override(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if ($request->isMethod('post')) {
            $l = Loan::find($loan->id);
            $l->balance = $request->balance;
            if (empty($request->override)) {
                $l->override = 0;
            } else {
                $l->override = $request->override;
            }
            $l->save();
            GeneralHelper::audit_trail("Override balance for loan with id:" . $loan->id);
            Flash::success(trans('general.override_successfully_applied'));
            return redirect('loan/' . $loan->id . '/show');
        }
        return view('loan.override', compact('loan'));
    }

    public function emailBorrowerStatement($borrower)
    {
        if (!empty($borrower->email)) {
            Mail::to($borrower->email)->send(new BorrowerStatement($borrower));
            Flash::success("Statement successfully sent");
            return redirect('borrower/' . $borrower->id . '/show');
        } else {
            Flash::warning("Borrower has no email set");
            return redirect('borrower/' . $borrower->id . '/show');
        }
    }

    public function emailLoanStatement($loan)
    {
        $borrower = $loan->borrower;
        if (!empty($borrower->email)) {
            Mail::to($borrower->email)->send(new LoanStatement($loan));
            Flash::success("Loan Statement successfully sent");
            return redirect('loan/' . $loan->id . '/show');
        } else {
            Flash::warning("Borrower has no email set");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function emailLoanSchedule($loan)
    {
        $borrower = $loan->borrower;
        if (!empty($borrower->email)) {
            Mail::to($borrower->email)->send(new \App\Mail\LoanSchedule($loan));
            Flash::success("Loan Statement successfully sent");
            return redirect('loan/' . $loan->id . '/show');
        } else {
            Flash::warning("Borrower has no email set");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

//loan applications
    public function indexApplication()
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $data = LoanApplication::where('branch_id', session('branch_id'))->get();

        return view('loan.applications', compact('data'));
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

        $query = DB::table("loan_applications")->leftJoin("borrowers", "borrowers.id", "loan_applications.borrower_id")->leftJoin("loan_products", "loan_products.id", "loan_applications.loan_product_id")->selectRaw(DB::raw("borrowers.first_name,borrowers.last_name,loan_applications.id,loan_applications.borrower_id,loan_applications.amount,loan_applications.created_at,loan_products.name loan_product,loan_applications.status"))->when($status, function ($query) use ($status) {
            $query->where("loan_applications.status", $status);
        })->when($borrower_id, function ($query) use ($borrower_id) {
            $query->where("loan_applications.borrower_id", $borrower_id);
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
            $action .= '<li><a href="' . url('loan/application/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/application/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'borrower', 'action', 'status'])->make(true);
    }

    public function declineApplication($id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $application = LoanApplication::find($id);
        $application->status = "declined";
        $application->save();
        GeneralHelper::audit_trail("Declined borrower  loan application with id:" . $id);
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('loan/loan_application/data');
    }

    public function deleteApplication($id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        LoanApplication::destroy($id);
        Guarantor::where('loan_application_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted borrower  loan application with id:" . $id);
        Flash::success(trans_choice('general.successfully_deleted', 1));
        return redirect('loan/loan_application/data');
    }

    public function approveApplication($id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $application = LoanApplication::find($id);
        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        $loan_product = $application->loan_product;
        $charges = array();
        foreach (LoanProductCharge::where('loan_product_id', $loan_product->id)->get() as $key) {
            if (!empty($key->charge)) {
                $charges[$key->id] = $key->charge->name;
            }

        }
        $users = [];
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        if (!empty($application->loan_product)) {
            return view('loan.approve_application',
                compact('id', 'application', 'loan_product', 'users', 'custom_fields', 'charges'));

        } else {
            Flash::warning(trans_choice('general.loan_application_approve_error', 1));
            return redirect('loan/loan_application/data');
        }
    }

    public function storeApproveApplication(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $application = LoanApplication::find($id);
        $application->status = "approved";
        $application->save();
        //lets save the loan here
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $loan = new Loan();
        $loan->principal = $request->principal;
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $request->borrower_id;
        $loan->branch_id = $application->branch_id;
        $loan->applied_amount = $request->principal;
        $loan->user_id = Sentinel::getUser()->id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        if (!empty($request->first_payment_date)) {
            $loan->first_payment_date = $request->first_payment_date;
        }
        $loan->description = $request->description;
        $files = array();
        if (!empty($request->file('files'))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $files[$count] = $key->getClientOriginalName();
                    $key->move(public_path() . '/uploads',
                        $key->getClientOriginalName());
                }
                $count++;
            }
        }
        $loan->files = serialize($files);
        $loan->save();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $amount = "charge_amount_" . $key;
                $date = "charge_date_" . $key;
                $loan_charge = new LoanCharge();
                $loan_charge->loan_id = $loan->id;
                $loan_charge->user_id = Sentinel::getUser()->id;
                $loan_charge->charge_id = $key;
                $loan_charge->amount = $request->$amount;
                if (!empty($request->$date)) {
                    $loan_charge->date = $request->$date;
                }
                $loan_charge->save();
            }
        }
        //save custom meta
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $loan->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }

        //lets create schedules here
        //determine interest rate to use

        $interest_rate = GeneralHelper::determine_interest_rate($loan->id);

        $period = GeneralHelper::loan_period($loan->id);
        $loan = Loan::find($loan->id);
        if ($loan->repayment_cycle == 'daily') {
            $repayment_cycle = 'day';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' days')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'weekly') {
            $repayment_cycle = 'week';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' weeks')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'quarterly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'yearly') {
            $repayment_cycle = 'year';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' years')),
                'Y-m-d');
        }
        $loan->save();

        //generate schedules until period finished
        $next_payment = $request->first_payment_date;
        $balance = $request->principal;
        GeneralHelper::audit_trail("Approved borrower  loan application with id:" . $id);
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('loan/loan_application/data');
    }

//loan calculator
    public function createLoanCalculator(Request $request)
    {
        if (!Sentinel::hasAccess('loans.loan_calculator')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        $loan_fees = LoanFee::all();
        return view('loan_calculator/create',
            compact('loan_fees'));
    }

    public function showLoanCalculator(Request $request)
    {
        if (!Sentinel::hasAccess('loans.loan_calculator')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan_calculator/show',
            compact('request'));
    }

    public function storeLoanCalculator(Request $request)
    {
        if (!Sentinel::hasAccess('loans.loan_calculator')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        if (!empty($request->pdf)) {
            $pdf = PDF::loadView('loan_calculator.pdf', compact('request'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download("Calculated Schedule.pdf");

        }
        if (!empty($request->print)) {
            return view('loan_calculator.print', compact('request'));
        }

    }

    public function add_guarantor(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('loans.guarantor.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $guarantor = new LoanGuarantor();
        $guarantor->guarantor_id = $request->guarantor_id;
        $guarantor->loan_id = $loan->id;;
        $guarantor->user_id = Sentinel::getUser()->id;
        $guarantor->borrower_id = $loan->borrower_id;
        $guarantor->save();
        GeneralHelper::audit_trail("Added guarantor for loan with id:" . $loan->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function remove_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantor.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        LoanGuarantor::destroy($id);
        GeneralHelper::audit_trail("Removed guarantor for loan with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }
}
