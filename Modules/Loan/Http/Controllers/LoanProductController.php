<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Core\Entities\Currency;
use Modules\Loan\Entities\Fund;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanCreditCheck;
use Modules\Loan\Entities\LoanProduct;
use JavaScript;
use Modules\Loan\Entities\LoanProductLinkedCharge;
use Modules\Loan\Entities\LoanProductLinkedCreditCheck;
use Modules\Loan\Entities\LoanTransactionProcessingStrategy;
use Yajra\DataTables\Facades\DataTables;

class LoanProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.products.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.products.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.products.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.products.destroy'])->only(['destroy']);

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
        $data = LoanProduct::leftJoin('currencies', 'currencies.id', 'loan_products.currency_id')
            ->leftJoin('funds', 'funds.id', 'loan_products.fund_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('loan_products.name', 'like', "%$search%");
            })
            ->selectRaw("loan_products.*,currencies.name currency,funds.name fund")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::loan_product.index', compact('data'));
    }

    public function get_products(Request $request)
    {
        $query = LoanProduct::leftJoin('currencies', 'currencies.id', 'loan_products.currency_id')
            ->leftJoin('funds', 'funds.id', 'loan_products.fund_id')
            ->selectRaw("loan_products.*,currencies.name currency,funds.name fund");

        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('loan.loans.products.edit')) {
                $action .= '<li><a href="' . url('loan/product/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('loan.loans.products.destroy')) {
                $action .= '<li><a href="' . url('loan/product/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('active', function ($data) {
            if ($data->active == 1) {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->active == 0) {
                return trans_choice('core::general.no', 1);
            }
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/product/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $funds = Fund::all();
        $currencies = Currency::orderBy('name')->get();
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        $income = ChartOfAccount::where('account_type', 'income')->get();
        $liabilities = ChartOfAccount::where('account_type', 'liability')->get();
        $credit_checks = LoanCreditCheck::where('active', 1)->get();
        $loan_charges = LoanCharge::where('active', 1)->get();
        $loan_transaction_processing_strategies = LoanTransactionProcessingStrategy::orderBy('id')->get();
        JavaScript::put([
            'loan_credit_checks' => $credit_checks,
            'loan_charges' => $loan_charges,
            'currencies' => $currencies
        ]);
        return theme_view('loan::loan_product.create', compact('funds', 'currencies', 'expenses', 'income', 'liabilities', 'assets', 'credit_checks', 'loan_charges', 'loan_transaction_processing_strategies'));
    }

    public function get_charges($id)
    {

        $charges = LoanCharge::where('active', 1)->where('currency_id', $id)->get();
        return response()->json(["collection" => $charges]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'fund_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric', 'lt:maximum_principal'],
            'default_principal' => ['required', 'numeric', 'lt:maximum_principal'],
            'maximum_principal' => ['required', 'numeric', 'gt:minimum_principal'],
            'minimum_loan_term' => ['required', 'numeric', 'lt:maximum_loan_term'],
            'default_loan_term' => ['required', 'numeric', 'lt:maximum_loan_term'],
            'maximum_loan_term' => ['required', 'numeric', 'gt:minimum_loan_term'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric', 'lt:maximum_interest_rate'],
            'default_interest_rate' => ['required', 'numeric', 'lt:maximum_interest_rate'],
            'maximum_interest_rate' => ['required', 'numeric', 'gt:minimum_interest_rate'],
            'interest_rate_type' => ['required'],
            'grace_on_principal_paid' => ['required'],
            'grace_on_interest_paid' => ['required'],
            'grace_on_interest_charged' => ['required'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'auto_disburse' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'fund_source_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'loan_portfolio_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ], [
            'minimum_principal.lt' => 'Minimum Principal cannot be greater than maximum principal',
            'default_principal.lte' => 'Default Principal cannot be greater than maximum principal',
            'maximum_principal.gt' => 'Maximum Principal cannot be smaller than minimum principal',
            'minimum_loan_term.lt' => 'Minimum loan term cannot be greater than maximum loan term',
            'default_loan_term.lte' => 'Default loan term cannot be greater than maximum loan term',
            'maximum_loan_term.gt' => 'Maximum loan term cannot be smaller than maximum loan term',
            'minimum_interest_rate.lt' => 'Minimum interest cannot be greater than maximum interest rate',
            'default_interest_rate.lte' => 'Default interest cannot be greater than maximum interest rate',
            'maximum_interest_rate.gt' => 'Maximum interest cannot be smaller than minimum interest rate',
            'minimum_principal.numeric' => 'Minimum Principal must be a number',
            'default_principal.numeric' => 'Default Principal must be a number',
            'maximum_principal.numeric' => 'Maximum Principal must be a number',
            'minimum_loan_term.numeric' => 'Minimum loan term must be a number',
            'default_loan_term.numeric' => 'Default loan term must be a number',
            'maximum_loan_term.numeric' => 'Maximum loan term must be a number',
            'minimum_interest_rate.numeric' => 'Minimum interest must be a number',
            'default_interest_rate.numeric' => 'Default interest must be a number',
            'maximum_interest_rate.numeric' => 'Maximum interest must be a number',
            'repayment_frequency.numeric' => 'Repayment frequency must be a number',
        ]);
        $loan_product = new LoanProduct();
        $loan_product->currency_id = $request->currency_id;
        $loan_product->loan_transaction_processing_strategy_id = $request->loan_transaction_processing_strategy_id;
        $loan_product->fund_id = $request->fund_id;
        $loan_product->created_by_id = Auth::id();
        $loan_product->fund_source_chart_of_account_id = $request->fund_source_chart_of_account_id;
        $loan_product->loan_portfolio_chart_of_account_id = $request->loan_portfolio_chart_of_account_id;
        $loan_product->interest_receivable_chart_of_account_id = $request->interest_receivable_chart_of_account_id;
        $loan_product->penalties_receivable_chart_of_account_id = $request->penalties_receivable_chart_of_account_id;
        $loan_product->fees_receivable_chart_of_account_id = $request->fees_receivable_chart_of_account_id;
        $loan_product->fees_chart_of_account_id = $request->fees_chart_of_account_id;
        $loan_product->overpayments_chart_of_account_id = $request->overpayments_chart_of_account_id;
        $loan_product->income_from_interest_chart_of_account_id = $request->income_from_interest_chart_of_account_id;
        $loan_product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
        $loan_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $loan_product->income_from_recovery_chart_of_account_id = $request->income_from_recovery_chart_of_account_id;
        $loan_product->losses_written_off_chart_of_account_id = $request->losses_written_off_chart_of_account_id;
        $loan_product->interest_written_off_chart_of_account_id = $request->interest_written_off_chart_of_account_id;
        $loan_product->suspended_income_chart_of_account_id = $request->suspended_income_chart_of_account_id;
        $loan_product->name = $request->name;
        $loan_product->short_name = $request->short_name;
        $loan_product->description = $request->description;
        $loan_product->decimals = $request->decimals;
        $loan_product->minimum_principal = $request->minimum_principal;
        $loan_product->default_principal = $request->default_principal;
        $loan_product->maximum_principal = $request->maximum_principal;
        $loan_product->minimum_loan_term = $request->minimum_loan_term;
        $loan_product->default_loan_term = $request->default_loan_term;
        $loan_product->maximum_loan_term = $request->maximum_loan_term;
        $loan_product->repayment_frequency = $request->repayment_frequency;
        $loan_product->repayment_frequency_type = $request->repayment_frequency_type;
        $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
        $loan_product->default_interest_rate = $request->default_interest_rate;
        $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
        $loan_product->interest_rate_type = $request->interest_rate_type;
        $loan_product->grace_on_principal_paid = $request->grace_on_principal_paid;
        $loan_product->grace_on_interest_paid = $request->grace_on_interest_paid;
        $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan_product->interest_methodology = $request->interest_methodology;
        $loan_product->amortization_method = $request->amortization_method;
        $loan_product->accounting_rule = $request->accounting_rule;
        $loan_product->auto_disburse = $request->auto_disburse;
        $loan_product->active = $request->active;
        $loan_product->save();
        //save charges
        if (!empty($request->charges)) {
            foreach (explode(',',$request->charges) as $key) {
                if (!empty($key)) {
                    $loan_product_charge = new LoanProductLinkedCharge();
                    $loan_product_charge->loan_product_id = $loan_product->id;
                    $loan_product_charge->loan_charge_id = $key;
                    $loan_product_charge->save();
                }
            }
        }
        //save credit checks
        if (!empty($request->credit_checks)) {

            foreach (explode(',',$request->credit_checks) as $key) {
                if (!empty($key)) {
                    $loan_product_credit_check = new LoanProductLinkedCreditCheck();
                    $loan_product_credit_check->loan_product_id = $loan_product->id;
                    $loan_product_credit_check->loan_credit_check_id = $key;
                    $loan_product_credit_check->save();
                }
            }
        }
        activity()->on($loan_product)
            ->withProperties(['id' => $loan_product->id])
            ->log('Create Loan Product');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/product');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('loan::loan_product.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_product = LoanProduct::find($id);
        $funds = Fund::all();
        $currencies = Currency::orderBy('name')->get();
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        $income = ChartOfAccount::where('account_type', 'income')->get();
        $liabilities = ChartOfAccount::where('account_type', 'liability')->get();
        $credit_checks = LoanCreditCheck::where('active', 1)->get();
        $loan_charges = LoanCharge::where('active', 1)->get();
        $selected_charges = [];
        foreach ($loan_product->charges as $key) {
            $selected_charges[] = $key->loan_charge_id;
        }
        $loan_transaction_processing_strategies = LoanTransactionProcessingStrategy::orderBy('id')->get();
        JavaScript::put([
            'loan_credit_checks' => $credit_checks,
            'loan_charges' => $loan_charges,
            'currencies' => $currencies,
        ]);
        return theme_view('loan::loan_product.edit', compact('funds', 'currencies', 'expenses', 'income', 'liabilities', 'assets', 'credit_checks', 'loan_charges', 'loan_transaction_processing_strategies', 'loan_product', 'selected_charges'));
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
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'fund_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric', 'lt:maximum_principal'],
            'default_principal' => ['required', 'numeric', 'lte:maximum_principal'],
            'maximum_principal' => ['required', 'numeric', 'gt:minimum_principal'],
            'minimum_loan_term' => ['required', 'numeric', 'lt:maximum_loan_term'],
            'default_loan_term' => ['required', 'numeric', 'lte:maximum_loan_term'],
            'maximum_loan_term' => ['required', 'numeric', 'gt:minimum_loan_term'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric', 'lt:maximum_interest_rate'],
            'default_interest_rate' => ['required', 'numeric', 'lte:maximum_interest_rate'],
            'maximum_interest_rate' => ['required', 'numeric', 'gt:minimum_interest_rate'],
            'interest_rate_type' => ['required'],
            'grace_on_principal_paid' => ['required'],
            'grace_on_interest_paid' => ['required'],
            'grace_on_interest_charged' => ['required'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'auto_disburse' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'fund_source_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'loan_portfolio_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ], [
            'minimum_principal.lt' => 'Minimum Principal cannot be greater than maximum principal',
            'default_principal.lte' => 'Default Principal cannot be greater than maximum principal',
            'maximum_principal.gt' => 'Maximum Principal cannot be smaller than minimum principal',
            'minimum_loan_term.lt' => 'Minimum loan term cannot be greater than maximum loan term',
            'default_loan_term.lte' => 'Default loan term cannot be greater than maximum loan term',
            'maximum_loan_term.gt' => 'Maximum loan term cannot be smaller than maximum loan term',
            'minimum_interest_rate.lt' => 'Minimum interest cannot be greater than maximum interest rate',
            'default_interest_rate.lte' => 'Default interest cannot be greater than maximum interest rate',
            'maximum_interest_rate.gt' => 'Maximum interest cannot be smaller than minimum interest rate',
            'minimum_principal.numeric' => 'Minimum Principal must be a number',
            'default_principal.numeric' => 'Default Principal must be a number',
            'maximum_principal.numeric' => 'Maximum Principal must be a number',
            'minimum_loan_term.numeric' => 'Minimum loan term must be a number',
            'default_loan_term.numeric' => 'Default loan term must be a number',
            'maximum_loan_term.numeric' => 'Maximum loan term must be a number',
            'minimum_interest_rate.numeric' => 'Minimum interest must be a number',
            'default_interest_rate.numeric' => 'Default interest must be a number',
            'maximum_interest_rate.numeric' => 'Maximum interest must be a number',
            'repayment_frequency.numeric' => 'Repayment frequency must be a number',

        ]);
        $loan_product = LoanProduct::find($id);
        $loan_product->currency_id = $request->currency_id;
        $loan_product->loan_transaction_processing_strategy_id = $request->loan_transaction_processing_strategy_id;
        $loan_product->fund_id = $request->fund_id;
        $loan_product->fund_source_chart_of_account_id = $request->fund_source_chart_of_account_id;
        $loan_product->loan_portfolio_chart_of_account_id = $request->loan_portfolio_chart_of_account_id;
        $loan_product->interest_receivable_chart_of_account_id = $request->interest_receivable_chart_of_account_id;
        $loan_product->penalties_receivable_chart_of_account_id = $request->penalties_receivable_chart_of_account_id;
        $loan_product->fees_receivable_chart_of_account_id = $request->fees_receivable_chart_of_account_id;
        $loan_product->fees_chart_of_account_id = $request->fees_chart_of_account_id;
        $loan_product->overpayments_chart_of_account_id = $request->overpayments_chart_of_account_id;
        $loan_product->income_from_interest_chart_of_account_id = $request->income_from_interest_chart_of_account_id;
        $loan_product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
        $loan_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $loan_product->income_from_recovery_chart_of_account_id = $request->income_from_recovery_chart_of_account_id;
        $loan_product->losses_written_off_chart_of_account_id = $request->losses_written_off_chart_of_account_id;
        $loan_product->interest_written_off_chart_of_account_id = $request->interest_written_off_chart_of_account_id;
        $loan_product->suspended_income_chart_of_account_id = $request->suspended_income_chart_of_account_id;
        $loan_product->name = $request->name;
        $loan_product->short_name = $request->short_name;
        $loan_product->description = $request->description;
        $loan_product->decimals = $request->decimals;
        $loan_product->minimum_principal = $request->minimum_principal;
        $loan_product->default_principal = $request->default_principal;
        $loan_product->maximum_principal = $request->maximum_principal;
        $loan_product->minimum_loan_term = $request->minimum_loan_term;
        $loan_product->default_loan_term = $request->default_loan_term;
        $loan_product->maximum_loan_term = $request->maximum_loan_term;
        $loan_product->repayment_frequency = $request->repayment_frequency;
        $loan_product->repayment_frequency_type = $request->repayment_frequency_type;
        $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
        $loan_product->default_interest_rate = $request->default_interest_rate;
        $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
        $loan_product->interest_rate_type = $request->interest_rate_type;
        $loan_product->grace_on_principal_paid = $request->grace_on_principal_paid;
        $loan_product->grace_on_interest_paid = $request->grace_on_interest_paid;
        $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan_product->interest_methodology = $request->interest_methodology;
        $loan_product->amortization_method = $request->amortization_method;
        $loan_product->accounting_rule = $request->accounting_rule;
        $loan_product->auto_disburse = $request->auto_disburse;
        $loan_product->active = $request->active;
        $loan_product->save();
        //save charges
        LoanProductLinkedCharge::where('loan_product_id', $loan_product->id)->delete();
        if (!empty($request->charges)) {
            foreach (explode(',',$request->charges) as $key) {
                if (!empty($key)) {
                    $loan_product_charge = new LoanProductLinkedCharge();
                    $loan_product_charge->loan_product_id = $loan_product->id;
                    $loan_product_charge->loan_charge_id = $key;
                    $loan_product_charge->save();
                }
            }
        }
        //save credit checks
        LoanProductLinkedCreditCheck::where('loan_product_id', $loan_product->id)->delete();
        //save credit checks
        if (!empty($request->credit_checks)) {
            foreach (explode(',',$request->credit_checks) as $key) {
                if (!empty($key)) {
                    $loan_product_credit_check = new LoanProductLinkedCreditCheck();
                    $loan_product_credit_check->loan_product_id = $loan_product->id;
                    $loan_product_credit_check->loan_credit_check_id = $key;
                    $loan_product_credit_check->save();
                }
            }
        }
        activity()->on($loan_product)
            ->withProperties(['id' => $loan_product->id])
            ->log('Update Loan Product');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/product');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_product = LoanProduct::find($id);
        $loan_product->delete();
        activity()->on($loan_product)
            ->withProperties(['id' => $loan_product->id])
            ->log('Delete Loan Product');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
