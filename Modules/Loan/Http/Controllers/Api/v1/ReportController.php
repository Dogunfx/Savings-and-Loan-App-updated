<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Entities\Loan;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.reports.repayments'])->only(['repayment']);
        $this->middleware(['permission:loan.loans.reports.collection_sheet'])->only(['collection_sheet']);
        $this->middleware(['permission:loan.loans.reports.expected_repayments'])->only(['expected_repayment']);
        $this->middleware(['permission:loan.loans.reports.arrears'])->only(['arrears']);
        $this->middleware(['permission:loan.loans.reports.disbursements'])->only(['disbursement']);
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function collection_sheet(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $loan_product_id = $request->loan_product_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loan_repayment_schedules.loan_id,loans.expected_maturity_date,loan_repayment_schedules.total_due,(loan_repayment_schedules.principal+loan_repayment_schedules.interest+loan_repayment_schedules.fees+loan_repayment_schedules.penalties-loan_repayment_schedules.principal_written_off_derived-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.interest_waived_derived-loan_repayment_schedules.fees_waived_derived-loan_repayment_schedules.penalties_waived_derived) expected_amount,loan_repayment_schedules.due_date")
                ->get();
        }
        return response()->json(['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function repayment(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;

        $data = [];

        if (!empty($start_date)) {
            $data = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_transactions.submitted_on', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loan_transaction_type_id', 2)
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,loans.client_id,loan_transactions.id,loan_transactions.loan_id,loan_transactions.principal_repaid_derived,loan_transactions.interest_repaid_derived,loan_transactions.fees_repaid_derived,loan_transactions.penalties_repaid_derived,loan_transactions.submitted_on,payment_types.name payment_type")
                ->get();
        }
        return response()->json(['data' => $data]);
    }

    public function expected_repayment(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];

        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("branches.name branch,loans.branch_id,coalesce(sum(loan_repayment_schedules.principal-loan_repayment_schedules.principal_written_off_derived),0) principal,coalesce(sum(loan_repayment_schedules.interest-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.interest_waived_derived),0) interest,coalesce(sum(loan_repayment_schedules.fees-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.fees_waived_derived),0) fees,coalesce(sum(loan_repayment_schedules.penalties-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.penalties_waived_derived),0) penalties,coalesce(sum(loan_repayment_schedules.principal_repaid_derived),0) principal_repaid_derived,coalesce(sum(loan_repayment_schedules.interest_repaid_derived),0) interest_repaid_derived,coalesce(sum(loan_repayment_schedules.fees_repaid_derived),0) fees_repaid_derived,coalesce(sum(loan_repayment_schedules.penalties_repaid_derived),0) penalties_repaid_derived")
                ->groupBy('branches.id')
                ->get();

        }
        return response()->json(['data' => $data]);
    }

    public function arrears(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        if (!empty($end_date)) {
            $data = Loan::with("repayment_schedules")
                ->join(DB::raw("(select*from loan_repayment_schedules where loan_repayment_schedules.due_date<'$end_date' and total_due>0) loan_repayment_schedules"), "loan_repayment_schedules.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,clients.mobile,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,(SELECT submitted_on FROM loan_transactions WHERE loan_id=loans.id ORDER BY submitted_on DESC LIMIT 1) last_payment_date,loans.principal")
                ->groupBy('loans.id')
                ->get();

        }
        return response()->json(['data' => $data]);
    }

    public function disbursement(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($start_date)) {
            $data = Loan::with("repayment_schedules")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("funds", "loans.fund_id", "funds.id")
                ->join("loan_purposes", "loans.loan_purpose_id", "loan_purposes.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.disbursed_on_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('loans.status', $status);
                })
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,clients.gender,clients.dob,clients.mobile,concat(users.first_name,' ',users.last_name) loan_officer,loan_purposes.name loan_purpose,funds.name fund,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,loans.principal,loans.status,loans.repayment_frequency,loans.repayment_frequency_type")
                ->get();

        }
        return response()->json(['data' => $data]);
    }
}
