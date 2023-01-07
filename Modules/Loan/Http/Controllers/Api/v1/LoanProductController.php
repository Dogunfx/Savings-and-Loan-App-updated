<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        $this->middleware('auth:api');
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
        $limit = $request->limit ? $request->limit : 20;
        $data = LoanProduct::paginate($limit);
        return response()->json([$data]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function get_charges($id)
    {

        $charges = LoanCharge::where('active', 1)->where('currency_id', $id)->get();
        return response()->json(["data" => $charges]);
    }

    public function get_loan_transaction_processing_strategies()
    {

        $charges = LoanTransactionProcessingStrategy::orderBy('id')->get();
        return response()->json(["data" => $charges]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'fund_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric'],
            'default_principal' => ['required', 'numeric'],
            'maximum_principal' => ['required', 'numeric'],
            'minimum_loan_term' => ['required', 'numeric'],
            'default_loan_term' => ['required', 'numeric'],
            'maximum_loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric'],
            'default_interest_rate' => ['required', 'numeric'],
            'maximum_interest_rate' => ['required', 'numeric'],
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
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
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
                foreach ($request->charges as $key) {
                    $loan_product_charge = new LoanProductLinkedCharge();
                    $loan_product_charge->loan_product_id = $loan_product->id;
                    $loan_product_charge->loan_charge_id = $key;
                    $loan_product_charge->save();
                }
            }
            //save credit checks
            if (!empty($request->credit_checks)) {
                foreach ($request->credit_checks as $key) {
                    $loan_product_credit_check = new LoanProductLinkedCreditCheck();
                    $loan_product_credit_check->loan_product_id = $loan_product->id;
                    $loan_product_credit_check->loan_credit_check_id = $key;
                    $loan_product_credit_check->save();
                }
            }
            return response()->json(['data' => $loan_product, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_product = LoanProduct::find($id);
        return response()->json(['data' => $loan_product]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_product = LoanProduct::with('charges')->find($id);
        return response()->json(['data' => $loan_product]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $loan_product = LoanProduct::find($id);
        $validator = Validator::make($request->all(), [
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'fund_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric'],
            'default_principal' => ['required', 'numeric'],
            'maximum_principal' => ['required', 'numeric'],
            'minimum_loan_term' => ['required', 'numeric'],
            'default_loan_term' => ['required', 'numeric'],
            'maximum_loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric'],
            'default_interest_rate' => ['required', 'numeric'],
            'maximum_interest_rate' => ['required', 'numeric'],
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
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {

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
                foreach ($request->charges as $key) {
                    $loan_product_charge = new LoanProductLinkedCharge();
                    $loan_product_charge->loan_product_id = $loan_product->id;
                    $loan_product_charge->loan_charge_id = $key;
                    $loan_product_charge->save();
                }
            }
            //save credit checks
            LoanProductLinkedCreditCheck::where('loan_product_id', $loan_product->id)->delete();
            if (!empty($request->credit_checks)) {
                foreach ($request->credit_checks as $key) {
                    $loan_product_credit_check = new LoanProductLinkedCreditCheck();
                    $loan_product_credit_check->loan_product_id = $loan_product->id;
                    $loan_product_credit_check->loan_credit_check_id = $key;
                    $loan_product_credit_check->save();
                }
            }
            return response()->json(['data' => $loan_product, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        LoanProduct::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
