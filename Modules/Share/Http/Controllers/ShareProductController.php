<?php

namespace Modules\Share\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Core\Entities\Currency;
use Modules\Share\Entities\ShareCharge;
use Modules\Share\Entities\ShareProduct;
use Modules\Share\Entities\ShareProductLinkedCharge;
use Yajra\DataTables\Facades\DataTables;

class ShareProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:share.shares.products.index'])->only(['index', 'show']);
        $this->middleware(['permission:share.shares.products.create'])->only(['create', 'store']);
        $this->middleware(['permission:share.shares.products.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:share.shares.products.destroy'])->only(['destroy']);

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
        $data = ShareProduct::leftJoin('currencies', 'currencies.id', 'share_products.currency_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('share_products.name', 'like', "%$search%");
            })
            ->selectRaw("share_products.*,currencies.name currency")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('share::share_product.index', compact('data'));
    }

    public function get_products(Request $request)
    {
        $query = ShareProduct::leftJoin('currencies', 'currencies.id', 'share_products.currency_id')
            ->selectRaw("share_products.*,currencies.name currency");

        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('share.shares.products.edit')) {
                $action .= '<li><a href="' . url('share/product/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('share.shares.products.destroy')) {
                $action .= '<li><a href="' . url('share/product/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
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
            return '<a href="' . url('share/product/' . $data->id . '/show') . '">' . $data->id . '</a>';

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
        $equities = ChartOfAccount::where('account_type', 'equity')->get();
        $share_charges = ShareCharge::where('active', 1)->get();
        \JavaScript::put([
            'share_charges' => $share_charges,
            'currencies' => $currencies
        ]);
        return theme_view('share::share_product.create', compact('currencies', 'expenses', 'income', 'liabilities', 'assets', 'share_charges', 'equities'));
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
            'total_shares' => ['required', 'numeric'],
            'shares_to_be_issued' => ['required', 'numeric'],
            'nominal_price' => ['required', 'numeric'],
            'lockin_period' => ['required', 'numeric'],
            'lockin_type' => ['required'],
            'decimals' => ['required', 'numeric'],
            'minimum_shares_per_client' => ['required'],
            'default_shares_per_client' => ['required'],
            'maximum_shares_per_client' => ['required'],
            'minimum_active_period' => ['required'],
            'minimum_active_period_type' => ['required'],
            'allow_dividends_for_inactive_clients' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'share_reference_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'share_suspense_control_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'equity_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ]);
        $share_product = new ShareProduct();
        $share_product->created_by_id = Auth::id();
        $share_product->currency_id = $request->currency_id;
        $share_product->share_reference_chart_of_account_id = $request->share_reference_chart_of_account_id;
        $share_product->share_suspense_control_chart_of_account_id = $request->share_suspense_control_chart_of_account_id;
        $share_product->equity_chart_of_account_id = $request->equity_chart_of_account_id;
        $share_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $share_product->total_shares = $request->total_shares;
        $share_product->name = $request->name;
        $share_product->short_name = $request->short_name;
        $share_product->description = $request->description;
        $share_product->decimals = $request->decimals;
        $share_product->shares_to_be_issued = $request->shares_to_be_issued;
        $share_product->nominal_price = $request->nominal_price;
        $share_product->capital_value = $request->nominal_price * $request->shares_to_be_issued;
        $share_product->lockin_period = $request->lockin_period;
        $share_product->lockin_type = $request->lockin_type;
        $share_product->minimum_shares_per_client = $request->minimum_shares_per_client;
        $share_product->default_shares_per_client = $request->default_shares_per_client;
        $share_product->maximum_shares_per_client = $request->maximum_shares_per_client;
        $share_product->minimum_active_period = $request->minimum_active_period;
        $share_product->minimum_active_period_type = $request->minimum_active_period_type;
        $share_product->allow_dividends_for_inactive_clients = $request->allow_dividends_for_inactive_clients;
        $share_product->accounting_rule = $request->accounting_rule;
        $share_product->active = $request->active;
        $share_product->save();
        //save charges
        if (!empty($request->charges)) {
            foreach ($request->charges as $key) {
                if ($key) {
                    $share_product_charge = new ShareProductLinkedCharge();
                    $share_product_charge->share_product_id = $share_product->id;
                    $share_product_charge->share_charge_id = $key;
                    $share_product_charge->save();
                }
            }
        }
        activity()->on($share_product)
            ->withProperties(['id' => $share_product->id])
            ->log('Create Share Products');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/product');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('share::share_product.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $share_product = ShareProduct::find($id);
        $currencies = Currency::orderBy('name')->get();
        $assets = ChartOfAccount::where('account_type', 'asset')->get();
        $expenses = ChartOfAccount::where('account_type', 'expense')->get();
        $income = ChartOfAccount::where('account_type', 'income')->get();
        $liabilities = ChartOfAccount::where('account_type', 'liability')->get();
        $equities = ChartOfAccount::where('account_type', 'equity')->get();
        $share_charges = ShareCharge::where('active', 1)->get();
        $selected_charges = [];
        foreach ($share_product->charges as $key) {
            $selected_charges[] = $key->share_charge_id;
        }
        \JavaScript::put([
            'share_charges' => $share_charges,
            'currencies' => $currencies,
            'selected_charges' => $selected_charges
        ]);
        return theme_view('share::share_product.edit', compact('currencies', 'selected_charges', 'expenses', 'income', 'liabilities', 'assets', 'share_charges', 'share_product', 'equities'));
    }

    public function get_charges($id)
    {

        $charges = ShareCharge::where('active', 1)->where('currency_id', $id)->get();
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
            'total_shares' => ['required', 'numeric'],
            'shares_to_be_issued' => ['required', 'numeric'],
            'nominal_price' => ['required', 'numeric'],
            'lockin_period' => ['required', 'numeric'],
            'lockin_type' => ['required'],
            'decimals' => ['required', 'numeric'],
            'minimum_shares_per_client' => ['required'],
            'default_shares_per_client' => ['required'],
            'maximum_shares_per_client' => ['required'],
            'minimum_active_period' => ['required'],
            'minimum_active_period_type' => ['required'],
            'allow_dividends_for_inactive_clients' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'share_reference_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'share_suspense_control_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'equity_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ]);
        $share_product = ShareProduct::find($id);
        $share_product->currency_id = $request->currency_id;
        $share_product->share_reference_chart_of_account_id = $request->share_reference_chart_of_account_id;
        $share_product->share_suspense_control_chart_of_account_id = $request->share_suspense_control_chart_of_account_id;
        $share_product->equity_chart_of_account_id = $request->equity_chart_of_account_id;
        $share_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $share_product->total_shares = $request->total_shares;
        $share_product->name = $request->name;
        $share_product->short_name = $request->short_name;
        $share_product->description = $request->description;
        $share_product->decimals = $request->decimals;
        $share_product->shares_to_be_issued = $request->shares_to_be_issued;
        $share_product->nominal_price = $request->nominal_price;
        $share_product->capital_value = $request->nominal_price * $request->shares_to_be_issued;
        $share_product->lockin_period = $request->lockin_period;
        $share_product->lockin_type = $request->lockin_type;
        $share_product->minimum_shares_per_client = $request->minimum_shares_per_client;
        $share_product->default_shares_per_client = $request->default_shares_per_client;
        $share_product->maximum_shares_per_client = $request->maximum_shares_per_client;
        $share_product->minimum_active_period = $request->minimum_active_period;
        $share_product->minimum_active_period_type = $request->minimum_active_period_type;
        $share_product->allow_dividends_for_inactive_clients = $request->allow_dividends_for_inactive_clients;
        $share_product->accounting_rule = $request->accounting_rule;
        $share_product->active = $request->active;
        $share_product->save();
        //save charges
        ShareProductLinkedCharge::where('share_product_id', $share_product->id)->delete();
        if (!empty($request->charges)) {
            foreach ($request->charges as $key) {
                if ($key) {
                    $share_product_charge = new ShareProductLinkedCharge();
                    $share_product_charge->share_product_id = $share_product->id;
                    $share_product_charge->share_charge_id = $key;
                    $share_product_charge->save();
                }
            }
        }
        activity()->on($share_product)
            ->withProperties(['id' => $share_product->id])
            ->log('Update Share Products');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/product');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $share_product = ShareProduct::find($id);
        $share_product->delete();
        activity()->on($share_product)
            ->withProperties(['id' => $share_product->id])
            ->log('Delete Share Products');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
