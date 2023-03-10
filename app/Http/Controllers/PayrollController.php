<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\ChartOfAccount;
use App\Models\CustomField;
use App\Models\JournalEntry;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use App\Models\PayrollTemplate;
use App\Models\PayrollTemplateMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends Controller
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
    public function index()
    {
        if (!Sentinel::hasAccess('payroll')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = User::all();
        return view('payroll.data', compact('data'));
    }

    public function get_payroll(Request $request)
    {
        if (!Sentinel::hasAccess('payroll')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!empty($request->user_id)) {
            $user_id = $request->user_id;
        } else {
            $user_id = null;
        }


        $query = DB::table("payroll")->leftJoin("users", "users.id", "payroll.user_id")->selectRaw(DB::raw("users.first_name,users.last_name,payroll.*,(SELECT SUM(value) FROM payroll_meta WHERE payroll_meta.payroll_id=payroll.id AND position='bottom_left') gross_amount,(SELECT SUM(value) FROM payroll_meta WHERE payroll_meta.payroll_id=payroll.id AND position='bottom_right') total_deductions"))->where('payroll.branch_id', session('branch_id'))->when($user_id, function ($query) use ($user_id) {
            $query->where("payroll.user_id", $user_id);
        });
        return DataTables::of($query)->editColumn('user', function ($data) {
            return '<a href="' . url('user/' . $data->user_id . '/show') . '">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })->editColumn('recurring', function ($data) {
            if ($data->recurring == 1) {
                return trans_choice('general.yes', 1);
            } else {
                return trans_choice('general.no', 1);
            }

        })->editColumn('gross_amount', function ($data) {
            return number_format($data->gross_amount, 2);
        })->editColumn('total_deductions', function ($data) {
            return number_format($data->total_deductions, 2);
        })->editColumn('net_amount', function ($data) {
            return number_format($data->gross_amount - $data->total_deductions, 2);
        })->editColumn('paid_amount', function ($data) {
            return number_format($data->paid_amount, 2);
        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Sentinel::hasAccess('payroll.view')) {
                $action .= '<li><a href="' . url('payroll/' . $data->id . '/payslip') . '" class="" target="_blank">' . trans_choice('general.payslip', 1) . '</a></li>';
            }
            if (Sentinel::hasAccess('payroll.update')) {
                $action .= '<li><a href="' . url('payroll/' . $data->id . '/edit') . '" class="">' . trans_choice('general.edit', 2) . '</a></li>';
            }
            if (Sentinel::hasAccess('payroll.delete')) {
                $action .= '<li><a href="' . url('payroll/' . $data->id . '/delete') . '" class="delete">' . trans_choice('general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('payroll/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'user', 'action'])->make(true);
    }

    public function indexTemplate()
    {
        $data = PayrollTemplate::all();
        return view('payroll.payroll_template.data', compact('data'));
    }

    public function editTemplate($id)
    {

        $top_left = PayrollTemplateMeta::where('payroll_template_id', $id)->where('position', 'top_left')->get();
        $top_right = PayrollTemplateMeta::where('payroll_template_id', $id)->where('position', 'top_right')->get();
        $bottom_left = PayrollTemplateMeta::where('payroll_template_id', $id)->where('position', 'bottom_left')->get();
        $bottom_right = PayrollTemplateMeta::where('payroll_template_id', $id)->where('position',
            'bottom_right')->get();
        return view('payroll.payroll_template.edit',
            compact('id', 'bottom_right', 'bottom_left', 'top_right', 'top_left'));
    }

    public function deleteTemplateMeta(Request $request)
    {
        PayrollTemplateMeta::destroy($request->meta_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('payroll.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $chart = [];
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }
        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        $chart_liability = array();
        foreach (ChartOfAccount::where('account_type', 'liability')->get() as $key) {
            $chart_liability[$key->id] = $key->name;
        }
        $chart_equity = array();
        foreach (ChartOfAccount::where('account_type', 'equity')->get() as $key) {
            $chart_equity[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        $chart[trans_choice('asset', 2)] = $chart_assets;
        $chart[trans_choice('income', 2)] = $chart_income;
        $chart[trans_choice('liability', 2)] = $chart_liability;
        $chart[trans_choice('equity', 2)] = $chart_equity;
        $chart[trans_choice('expense', 2)] = $chart_expenses;
        //get custom fields
        $custom_fields = CustomField::where('category', 'payroll')->get();
        $template = PayrollTemplate::first();
        $top_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_left')->get();
        $top_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_right')->get();
        return view('payroll.create',
            compact('user', 'custom_fields', 'bottom_right', 'bottom_left', 'top_right', 'top_left', 'template', 'chart'));
    }

    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('payroll.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $payroll = new Payroll();
        $payroll->payroll_template_id = $request->payroll_template_id;
        $payroll->user_id = $request->user_id;
        $payroll->employee_name = $request->employee_name;
        $payroll->business_name = $request->business_name;
        $payroll->payment_method = $request->payment_method;
        $payroll->branch_id = session('branch_id');
        $payroll->bank_name = $request->bank_name;
        $payroll->account_number = $request->account_number;
        $payroll->description = $request->description;
        $payroll->comments = $request->comments;
        $payroll->paid_amount = $request->paid_amount;
        $payroll->chart_id = $request->chart_id;
        $payroll->date = $request->date;
        $date = explode('-', $request->date);
        $payroll->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $payroll->recur_frequency = $request->recur_frequency;
            $payroll->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $payroll->recur_end_date = $request->recur_end_date;
            }

            $payroll->recur_next_date = date_format(date_add(date_create($request->recur_start_date),
                date_interval_create_from_date_string($request->recur_frequency . ' ' . $request->recur_type . 's')),
                'Y-m-d');

            $payroll->recur_type = $request->recur_type;
        }
        $payroll->year = $date[0];
        $payroll->month = $date[1];
        $payroll->save();
        //save payroll meta
        $metas = PayrollTemplateMeta::where('payroll_template_id', $request->template_id)->get();;
        foreach ($metas as $key) {
            $meta = new PayrollMeta();
            $kid = $key->id;
            $meta->value = $request->$kid;
            $meta->payroll_id = $payroll->id;
            $meta->payroll_template_meta_id = $key->id;
            $meta->position = $key->position;
            $meta->save();
        }
        //debit and credit the necessary accounts
        if (!empty(ChartOfAccount::find(Setting::where('setting_key', 'payroll_chart_id')->first()->setting_value))) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = Setting::where('setting_key', 'payroll_chart_id')->first()->setting_value;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_id = $payroll->id;
            $journal->debit = GeneralHelper::single_payroll_total_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->save();
        }
        if (!empty($payroll->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $payroll->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_id = $payroll->id;
            $journal->credit = GeneralHelper::single_payroll_total_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->save();
        }
        GeneralHelper::audit_trail("Added payroll with id:" . $payroll->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/data');
    }

    public function pdfPayslip($payroll)
    {

        $top_left = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'top_left')->get();
        $top_right = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'bottom_right')->get();
        $pdf = PDF::loadView('payroll.pdf_payslip',
            compact('payroll', 'top_left', 'top_right', 'bottom_left', 'bottom_right'));
        return $pdf->download($payroll->employee_name . " - Payslip.pdf",
            'D');


    }

    public function staffPayroll($user)
    {
        if (!Sentinel::hasAccess('payroll.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('payroll.staff_payroll', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addTemplateRow(Request $request, $id)
    {
        $meta = new PayrollTemplateMeta();
        $meta->name = $request->name;
        $meta->payroll_template_id = $id;
        $meta->position = $request->position;
        $meta->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/template/' . $id . '/edit');
    }

    public function getUser($id)
    {
        $user = User::find($id);
        return $user->first_name . ' ' . $user->last_name;
    }

    public function show($borrower)
    {
        if (!Sentinel::hasAccess('payroll.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.show', compact('borrower', 'user', 'custom_fields'));
    }


    public function edit($payroll)
    {
        if (!Sentinel::hasAccess('payroll.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $chart = [];
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }
        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        $chart_liability = array();
        foreach (ChartOfAccount::where('account_type', 'liability')->get() as $key) {
            $chart_liability[$key->id] = $key->name;
        }
        $chart_equity = array();
        foreach (ChartOfAccount::where('account_type', 'equity')->get() as $key) {
            $chart_equity[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        $chart[trans_choice('asset', 2)] = $chart_assets;
        $chart[trans_choice('income', 2)] = $chart_income;
        $chart[trans_choice('liability', 2)] = $chart_liability;
        $chart[trans_choice('equity', 2)] = $chart_equity;
        $chart[trans_choice('expense', 2)] = $chart_expenses;
        //get custom fields
        $custom_fields = CustomField::where('category', 'payroll')->get();
        $template = PayrollTemplate::first();
        $top_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_left')->get();
        $top_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_right')->get();
        return view('payroll.edit',
            compact('user', 'custom_fields', 'bottom_right', 'bottom_left', 'top_right', 'top_left', 'template',
                'payroll', 'chart'));
    }

    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('payroll.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $payroll = Payroll::find($id);
        $payroll->payroll_template_id = $request->payroll_template_id;
        $payroll->user_id = $request->user_id;
        $payroll->employee_name = $request->employee_name;
        $payroll->business_name = $request->business_name;
        $payroll->payment_method = $request->payment_method;
        $payroll->bank_name = $request->bank_name;
        $payroll->account_number = $request->account_number;
        $payroll->description = $request->description;
        $payroll->comments = $request->comments;
        $payroll->paid_amount = $request->paid_amount;
        $payroll->date = $request->date;
        $payroll->chart_id = $request->chart_id;
        $date = explode('-', $request->date);
        $payroll->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $payroll->recur_frequency = $request->recur_frequency;
            $payroll->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $payroll->recur_end_date = $request->recur_end_date;
            }
            if (empty($payroll->recur_next_date)) {
                $payroll->recur_next_date = date_format(date_add(date_create($request->recur_start_date),
                    date_interval_create_from_date_string($request->recur_frequency . ' ' . $request->recur_type . 's')),
                    'Y-m-d');
            }
            $payroll->recur_type = $request->recur_type;
        }
        $payroll->year = $date[0];
        $payroll->month = $date[1];
        $payroll->save();
        //save payroll meta
        $metas = PayrollTemplateMeta::where('payroll_template_id', $request->template_id)->get();;
        foreach ($metas as $key) {
            if (!empty(PayrollMeta::where('payroll_template_meta_id', $key->id)->where('payroll_id',
                $id)->first())
            ) {
                $meta = PayrollMeta::where('payroll_template_meta_id', $key->id)->where('payroll_id',
                    $id)->first();
            } else {
                $meta = new PayrollMeta();
            }
            $kid = $key->id;
            $meta->value = $request->$kid;
            $meta->payroll_id = $payroll->id;
            $meta->payroll_template_meta_id = $key->id;
            $meta->position = $key->position;
            $meta->save();
        }
        //debit and credit the necessary accounts
        JournalEntry::where('payroll_id', $id)->delete();
        if (!empty(ChartOfAccount::find(Setting::where('setting_key', 'payroll_chart_id')->first()->setting_value))) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = Setting::where('setting_key', 'payroll_chart_id')->setting_value->first();
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_id = $payroll->id;
            $journal->debit = GeneralHelper::single_payroll_total_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->save();
        }
        if (!empty($payroll->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $payroll->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_id = $payroll->id;
            $journal->credit = GeneralHelper::single_payroll_total_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->save();
        }
        GeneralHelper::audit_trail("Updated payroll with id:" . $payroll->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/data');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateTemplate(Request $request, $id)
    {

        $metas = PayrollTemplateMeta::where('payroll_template_id', $id)->get();
        foreach ($metas as $key) {
            $meta = PayrollTemplateMeta::find($key->id);
            $kid = $key->id;
            $meta->name = $request->$kid;
            $meta->save();
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/template');
    }

    public function delete($id)
    {
        if (!Sentinel::hasAccess('payroll.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Payroll::destroy($id);
        PayrollMeta::where('payroll_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted payroll with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('payroll/data');
    }

}
