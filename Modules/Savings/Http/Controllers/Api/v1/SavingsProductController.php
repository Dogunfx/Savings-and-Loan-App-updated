<?php

namespace Modules\Savings\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Savings\Entities\SavingsCharge;
use Modules\Savings\Entities\SavingsProduct;
use Modules\Savings\Entities\SavingsProductLinkedCharge;

class SavingsProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:savings.savings.products.index'])->only(['index', 'show']);
        $this->middleware(['permission:savings.savings.products.create'])->only(['create', 'store']);
        $this->middleware(['permission:savings.savings.products.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:savings.savings.products.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = SavingsProduct::leftJoin('currencies', 'currencies.id', 'savings_products.currency_id')
            ->selectRaw("savings_products.*,currencies.name currency")->paginate($limit);
        return response()->json([$data]);
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
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'default_interest_rate' => ['required', 'numeric'],
            'automatic_opening_balance' => ['required', 'numeric'],
            'minimum_balance_for_interest_calculation' => ['required', 'numeric'],
            'lockin_period' => ['required', 'numeric'],
            'lockin_type' => ['required'],
            'decimals' => ['required', 'numeric'],
            'savings_category' => ['required'],
            'auto_create' => ['required'],
            'compounding_period' => ['required'],
            'interest_posting_period_type' => ['required'],
            'interest_calculation_type' => ['required'],
            'allow_overdraft' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'savings_control_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_on_savings_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'write_off_savings_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_overdraft_chart_of_account_id' => ['required_if:accounting_rule,cash'],

        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $savings_product = new SavingsProduct();
            $savings_product->currency_id = $request->currency_id;
            $savings_product->created_by_id = Auth::id();
            $savings_product->savings_reference_chart_of_account_id = $request->savings_reference_chart_of_account_id;
            $savings_product->overdraft_portfolio_chart_of_account_id = $request->overdraft_portfolio_chart_of_account_id;
            $savings_product->savings_control_chart_of_account_id = $request->savings_control_chart_of_account_id;
            $savings_product->interest_on_savings_chart_of_account_id = $request->interest_on_savings_chart_of_account_id;
            $savings_product->write_off_savings_chart_of_account_id = $request->write_off_savings_chart_of_account_id;
            $savings_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
            $savings_product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
            $savings_product->income_from_interest_overdraft_chart_of_account_id = $request->income_from_interest_overdraft_chart_of_account_id;
            $savings_product->savings_category = $request->savings_category;
            $savings_product->auto_create = $request->auto_create;
            $savings_product->default_interest_rate = $request->default_interest_rate;
            $savings_product->compounding_period = $request->compounding_period;
            $savings_product->interest_posting_period_type = $request->interest_posting_period_type;
            $savings_product->name = $request->name;
            $savings_product->short_name = $request->short_name;
            $savings_product->description = $request->description;
            $savings_product->decimals = $request->decimals;
            $savings_product->interest_calculation_type = $request->interest_calculation_type;
            $savings_product->automatic_opening_balance = $request->automatic_opening_balance;
            $savings_product->minimum_balance_for_interest_calculation = $request->minimum_balance_for_interest_calculation;
            $savings_product->lockin_period = $request->lockin_period;
            $savings_product->lockin_type = $request->lockin_type;
            $savings_product->allow_overdraft = $request->allow_overdraft;
            $savings_product->overdraft_limit = $request->overdraft_limit;
            $savings_product->overdraft_interest_rate = $request->overdraft_interest_rate;
            $savings_product->minimum_overdraft_for_interest = $request->minimum_overdraft_for_interest;
            $savings_product->accounting_rule = $request->accounting_rule;
            $savings_product->active = $request->active;
            $savings_product->save();
            //save charges
            if (!empty($request->charges)) {
                foreach ($request->charges as $key) {
                    $savings_product_charge = new SavingsProductLinkedCharge();
                    $savings_product_charge->savings_product_id = $savings_product->id;
                    $savings_product_charge->savings_charge_id = $key;
                    $savings_product_charge->save();
                }
            }
            return response()->json(['data' => $savings_product, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $savings_product = SavingsProduct::find($id);
        return response()->json(['data' => $savings_product]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $savings_product = SavingsProduct::find($id);
        return response()->json(['data' => $savings_product]);
    }

    public function get_charges($id)
    {

        $charges = SavingsCharge::where('active', 1)->where('currency_id', $id)->get();
        return response()->json(["data" => $charges]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'default_interest_rate' => ['required', 'numeric'],
            'automatic_opening_balance' => ['required', 'numeric'],
            'minimum_balance_for_interest_calculation' => ['required', 'numeric'],
            'lockin_period' => ['required', 'numeric'],
            'lockin_type' => ['required'],
            'decimals' => ['required', 'numeric'],
            'savings_category' => ['required'],
            'auto_create' => ['required'],
            'compounding_period' => ['required'],
            'interest_posting_period_type' => ['required'],
            'interest_calculation_type' => ['required'],
            'allow_overdraft' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'savings_control_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_on_savings_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'write_off_savings_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_overdraft_chart_of_account_id' => ['required_if:accounting_rule,cash'],

        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $savings_product = SavingsProduct::find($id);
            $savings_product->currency_id = $request->currency_id;
            $savings_product->savings_reference_chart_of_account_id = $request->savings_reference_chart_of_account_id;
            $savings_product->overdraft_portfolio_chart_of_account_id = $request->overdraft_portfolio_chart_of_account_id;
            $savings_product->savings_control_chart_of_account_id = $request->savings_control_chart_of_account_id;
            $savings_product->interest_on_savings_chart_of_account_id = $request->interest_on_savings_chart_of_account_id;
            $savings_product->write_off_savings_chart_of_account_id = $request->write_off_savings_chart_of_account_id;
            $savings_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
            $savings_product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
            $savings_product->income_from_interest_overdraft_chart_of_account_id = $request->income_from_interest_overdraft_chart_of_account_id;
            $savings_product->savings_category = $request->savings_category;
            $savings_product->auto_create = $request->auto_create;
            $savings_product->default_interest_rate = $request->default_interest_rate;
            $savings_product->compounding_period = $request->compounding_period;
            $savings_product->interest_posting_period_type = $request->interest_posting_period_type;
            $savings_product->name = $request->name;
            $savings_product->short_name = $request->short_name;
            $savings_product->description = $request->description;
            $savings_product->decimals = $request->decimals;
            $savings_product->interest_calculation_type = $request->interest_calculation_type;
            $savings_product->automatic_opening_balance = $request->automatic_opening_balance;
            $savings_product->minimum_balance_for_interest_calculation = $request->minimum_balance_for_interest_calculation;
            $savings_product->lockin_period = $request->lockin_period;
            $savings_product->lockin_type = $request->lockin_type;
            $savings_product->allow_overdraft = $request->allow_overdraft;
            $savings_product->overdraft_limit = $request->overdraft_limit;
            $savings_product->overdraft_interest_rate = $request->overdraft_interest_rate;
            $savings_product->minimum_overdraft_for_interest = $request->minimum_overdraft_for_interest;
            $savings_product->accounting_rule = $request->accounting_rule;
            $savings_product->active = $request->active;
            $savings_product->save();
            //save charges
            SavingsProductLinkedCharge::where('savings_product_id', $savings_product->id)->delete();
            if (!empty($request->charges)) {
                foreach ($request->charges as $key) {
                    $savings_product_charge = new SavingsProductLinkedCharge();
                    $savings_product_charge->savings_product_id = $savings_product->id;
                    $savings_product_charge->savings_charge_id = $key;
                    $savings_product_charge->save();
                }
            }
            return response()->json(['data' => $savings_product, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        SavingsProduct::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
