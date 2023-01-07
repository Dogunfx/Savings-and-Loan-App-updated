<?php

namespace Modules\Savings\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Core\Entities\Currency;
use Modules\Loan\Entities\LoanProduct;
use Modules\Savings\Entities\SavingsCharge;
use Modules\Savings\Entities\SavingsProduct;
use Modules\Savings\Entities\SavingsProductLinkedCharge;
use Yajra\DataTables\Facades\DataTables;

class SavingsProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
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
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = SavingsProduct::leftJoin('currencies', 'currencies.id', 'savings_products.currency_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('savings_products.name', 'like', "%$search%");
            })
            ->selectRaw("savings_products.*,currencies.name currency")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('savings::savings_product.index', compact('data'));
    }

    public function get_products(Request $request)
    {
        $query = SavingsProduct::leftJoin('currencies', 'currencies.id', 'savings_products.currency_id')
            ->selectRaw("savings_products.*,currencies.name currency");

        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('savings.savings.products.edit')) {
                $action .= '<li><a href="' . url('savings/product/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('savings.savings.products.destroy')) {
                $action .= '<li><a href="' . url('savings/product/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
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
            return '<a href="' . url('savings/product/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $currencies = Currency::orderBy('name')->get();
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        $income = ChartOfAccount::where('account_type', 'income')->get();
        $liabilities = ChartOfAccount::where('account_type', 'liability')->get();
        $savings_charges = SavingsCharge::where('active', 1)->get();
        \JavaScript::put([
            'savings_charges' => $savings_charges,
            'currencies' => $currencies
        ]);
        return theme_view('savings::savings_product.create', compact('currencies', 'expenses', 'income', 'liabilities', 'assets', 'savings_charges'));
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
                if ($key) {
                    $savings_product_charge = new SavingsProductLinkedCharge();
                    $savings_product_charge->savings_product_id = $savings_product->id;
                    $savings_product_charge->savings_charge_id = $key;
                    $savings_product_charge->save();
                }
            }
        }
        activity()->on($savings_product)
            ->withProperties(['id' => $savings_product->id])
            ->log('Create Savings Products');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('savings/product');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('savings::savings_product.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $savings_product = SavingsProduct::find($id);
        $currencies = Currency::orderBy('name')->get();
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        $income = ChartOfAccount::where('account_type', 'income')->get();
        $liabilities = ChartOfAccount::where('account_type', 'liability')->get();
        $savings_charges = SavingsCharge::where('active', 1)->get();
        $selected_charges = [];
        foreach ($savings_product->charges as $key) {
            $selected_charges[] = $key->savings_charge_id;
        }
        \JavaScript::put([
            'savings_charges' => $savings_charges,
            'currencies' => $currencies,
            'selected_charges' => $selected_charges
        ]);
        return theme_view('savings::savings_product.edit', compact('currencies', 'selected_charges', 'expenses', 'income', 'liabilities', 'assets', 'savings_charges', 'savings_product'));
    }

    public function get_charges($id)
    {

        $charges = SavingsCharge::where('active', 1)->where('currency_id', $id)->get();
        return response()->json(["collection" => $charges]);
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
                if ($key) {
                    $savings_product_charge = new SavingsProductLinkedCharge();
                    $savings_product_charge->savings_product_id = $savings_product->id;
                    $savings_product_charge->savings_charge_id = $key;
                    $savings_product_charge->save();
                }
            }
        }
        activity()->on($savings_product)
            ->withProperties(['id' => $savings_product->id])
            ->log('Update Savings Products');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('savings/product');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $savings_product = SavingsProduct::find($id);
        $savings_product->delete();
        activity()->on($savings_product)
            ->withProperties(['id' => $savings_product->id])
            ->log('Delete Savings Products');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
