<?php

namespace Modules\Accounting\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounting\Exports\AccountingExport;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Branch\Entities\Branch;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:accounting.reports.balance_sheet'])->only(['balance_sheet']);
        $this->middleware(['permission:accounting.reports.trial_balance'])->only(['trial_balance']);
        $this->middleware(['permission:accounting.reports.income_statement'])->only(['income_statement']);
        $this->middleware(['permission:accounting.reports.ledger'])->only(['ledger']);

    }

    public function index()
    {
        return theme_view('accounting::report.index');
    }

    public function trial_balance(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = DB::table("chart_of_accounts")->join("journal_entries", "journal_entries.chart_of_account_id", "chart_of_accounts.id")->join("branches", "journal_entries.branch_id", "branches.id")->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('journal_entries.date', [$start_date, $end_date]);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where('journal_entries.branch_id', $branch_id);
            })->where('chart_of_accounts.active', 1)->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,branches.name branch,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")->groupBy("chart_of_accounts.id")->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('accounting::report.trial_balance_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                    return $pdf->download(trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view('accounting::report.trial_balance_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.trial_balance', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view('accounting::report.trial_balance',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches'));
    }

    //income statement
    public function income_statement(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = DB::table("chart_of_accounts")->join("journal_entries", "journal_entries.chart_of_account_id", "chart_of_accounts.id")->join("branches", "journal_entries.branch_id", "branches.id")->when($start_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('journal_entries.date', [$start_date, $end_date]);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where('journal_entries.branch_id', $branch_id);
            })->where('chart_of_accounts.active', 1)->whereIn('chart_of_accounts.account_type', ['income', 'expense'])->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,branches.name branch,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")->groupBy("chart_of_accounts.id")->orderBy('account_type')->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('accounting::report.income_statement_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                    return $pdf->download(trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view('accounting::report.income_statement_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.income_statement', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view('accounting::report.income_statement',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches'));
    }

    //balance sheet
    public function balance_sheet(Request $request)
    {
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();
        if (!empty($end_date)) {
            $data = DB::table("chart_of_accounts")->leftJoin('journal_entries', function ($join) use ($end_date) {
                $join->on('journal_entries.chart_of_account_id', '=', 'chart_of_accounts.id')
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->where('journal_entries.date', '<=', $end_date);
                    });
            })->leftJoin('branches', function ($join) use ($branch_id) {
                $join->on('journal_entries.branch_id', '=', 'branches.id')
                    ->when($branch_id, function ($query) use ($branch_id) {
                        $query->where('journal_entries.branch_id', $branch_id);
                    });
            })->where('chart_of_accounts.active', 1)->whereIn('chart_of_accounts.account_type', ['asset', 'equity', 'liability'])->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,branches.name branch,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")->groupBy("chart_of_accounts.id")->orderBy('account_type')->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('accounting::report.balance_sheet_pdf'), compact('end_date', 'branch_id', 'data', 'branches'));
                    return $pdf->download(trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').pdf');
                }
                $view = theme_view('accounting::report.balance_sheet_pdf',
                    compact('end_date', 'branch_id', 'data', 'branches'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new AccountingExport($view), trans_choice('accounting::general.balance_sheet', 1) . '(' . $end_date . ').csv');
                }
            }
        }
        return theme_view('accounting::report.balance_sheet',
            compact('end_date', 'branch_id', 'data', 'branches'));
    }

    public function ledger(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();

        $data = DB::table("chart_of_accounts")
            ->leftJoin('journal_entries', function ($join) use ($end_date) {
                $join->on('journal_entries.chart_of_account_id', '=', 'chart_of_accounts.id')
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->where('journal_entries.date', '<=', $end_date);
                    });
            })
            ->leftJoin('branches', function ($join) use ($branch_id) {
                $join->on('journal_entries.branch_id', '=', 'branches.id')
                    ->when($branch_id, function ($query) use ($branch_id) {
                        $query->where('journal_entries.branch_id', $branch_id);
                    });
            })
            ->where('chart_of_accounts.active', 1)
            ->selectRaw("chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type,branches.name branch,SUM(journal_entries.debit) debit,SUM(journal_entries.credit) credit")
            ->groupBy("chart_of_accounts.id")
            ->orderBy('account_type')
            ->get();
        //check if we should download
        if ($request->download) {
            if ($request->type == 'pdf') {
                $pdf = PDF::loadView(theme_view_file('accounting::report.ledger_pdf'), compact('start_date', 'end_date', 'branch_id', 'data', 'branches'));
                return $pdf->download(trans_choice('accounting::general.ledger', 1) . '.pdf');
            }
            $view = theme_view('accounting::report.ledger_pdf',
                compact('start_date', 'end_date', 'branch_id', 'data', 'branches'));
            if ($request->type == 'excel_2007') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.ledger', 1) . '.xlsx');
            }
            if ($request->type == 'excel') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.ledger', 1) . '.xls');
            }
            if ($request->type == 'csv') {
                return Excel::download(new AccountingExport($view), trans_choice('accounting::general.ledger', 1) . '.csv');
            }
        }
        return theme_view('accounting::report.ledger',
            compact('start_date', 'end_date', 'branch_id', 'data', 'branches'));
    }

}
