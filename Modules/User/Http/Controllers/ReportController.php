<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsProduct;
use Modules\User\Exports\UserExport;
use Modules\User\Entities\User;
use PDF;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:user.reports.performance'])->only(['transaction']);
        $this->middleware(['permission:user.reports.index'])->only(['index']);
        $this->middleware(['permission:user.reports.accounts'])->only(['account']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('user::report.index');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function performance(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        $branches = Branch::all();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        if (!empty($start_date)) {
            $number_of_clients = Client::where('loan_officer_id', $loan_officer_id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->count();
            $number_of_loans = Loan::where('loan_officer_id', $loan_officer_id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->count();
            $number_of_savings = Savings::where('savings_officer_id', $loan_officer_id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->count();
            $disbursed_loans_amount = Loan::where('loan_officer_id', $loan_officer_id)
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('disbursed_on_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->sum('principal');
            $total_payments_received = LoanTransaction::join("loans", "loan_transactions.loan_id", "loans.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_transactions.submitted_on', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loan_officer_id', $loan_officer_id)
                ->where('loan_transaction_type_id', 2)
                ->sum('amount');
            $data = [
                'number_of_clients' => $number_of_clients,
                'number_of_loans' => $number_of_loans,
                'number_of_savings' => $number_of_savings,
                'disbursed_loans_amount' => $disbursed_loans_amount,
                'total_payments_received' => $total_payments_received,
            ];
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('user::report.performance_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches','users','loan_officer_id'));
                    return $pdf->download(trans_choice('user::general.performance_report', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view('user::report.performance_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches','users','loan_officer_id'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new UserExport($view), trans_choice('user::general.performance_report', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new UserExport($view), trans_choice('user::general.performance_report', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new UserExport($view), trans_choice('user::general.performance_report', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view('user::report.performance',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches', 'loan_officer_id', 'users'));
    }


}
