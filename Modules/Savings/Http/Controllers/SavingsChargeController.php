<?php

namespace Modules\Savings\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Currency;
use Modules\Savings\Entities\SavingsCharge;
use Modules\Savings\Entities\SavingsChargeOption;
use Modules\Savings\Entities\SavingsChargeType;
use Yajra\DataTables\Facades\DataTables;

class SavingsChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:savings.savings.charges.index'])->only(['index', 'show']);
        $this->middleware(['permission:savings.savings.charges.create'])->only(['create', 'store']);
        $this->middleware(['permission:savings.savings.charges.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:savings.savings.charges.destroy'])->only(['destroy']);

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
        $data = SavingsCharge::leftJoin('currencies', 'currencies.id', 'savings_charges.currency_id')
            ->leftJoin('savings_charge_types', 'savings_charge_types.id', 'savings_charges.savings_charge_type_id')
            ->leftJoin('savings_charge_options', 'savings_charge_options.id', 'savings_charges.savings_charge_option_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('savings_charges.name', 'like', "%$search%");
            })
            ->selectRaw("savings_charges.*,currencies.name currency,savings_charge_types.name charge_type,savings_charge_options.name charge_option")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('savings::savings_charge.index',compact('data'));
    }

    public function get_charges(Request $request)
    {
        $query = SavingsCharge::leftJoin('currencies', 'currencies.id', 'savings_charges.currency_id')
            ->leftJoin('savings_charge_types', 'savings_charge_types.id', 'savings_charges.savings_charge_type_id')
            ->leftJoin('savings_charge_options', 'savings_charge_options.id', 'savings_charges.savings_charge_option_id')
            ->selectRaw("savings_charges.*,currencies.name currency,savings_charge_types.name charge_type,savings_charge_options.name charge_option");

        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('savings.savings.charges.edit')) {
                $action .= '<li><a href="' . url('savings/charge/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('savings.savings.charges.destroy')) {
                $action .= '<li><a href="' . url('savings/charge/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('charge_type', function ($data) {
            if ($data->savings_charge_type_id == 1) {
                return trans_choice('savings::general.savings_activation', 1);
            }
            if ($data->savings_charge_type_id == 2) {
                return trans_choice('savings::general.specified_due_date', 1);
            }
            if ($data->savings_charge_type_id == 3) {
                return trans_choice('savings::general.withdrawal_fee', 1);
            }
            if ($data->savings_charge_type_id == 4) {
                return trans_choice('savings::general.annual_fee', 1);
            }
            if ($data->savings_charge_type_id == 5) {
                return trans_choice('savings::general.monthly_fee', 1);
            }
            if ($data->savings_charge_type_id == 6) {
                return trans_choice('savings::general.inactivity_fee', 1);
            }
            if ($data->savings_charge_type_id == 7) {
                return trans_choice('savings::general.quarterly_fee', 1);
            }
        })->editColumn('charge_option', function ($data) {
            if ($data->savings_charge_option_id == 1) {
                return number_format($data->amount, 2) . ' ' . trans_choice('savings::general.flat', 1);
            }
            if ($data->savings_charge_option_id == 2) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('savings::general.percentage_of_amount', 1);
            }
            if ($data->savings_charge_option_id == 3) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('savings::general.percentage_of_savings_balance', 1);
            }

        })->editColumn('active', function ($data) {
            if ($data->active == 1) {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->active == 0) {
                return trans_choice('core::general.no', 1);
            }
        })->editColumn('id', function ($data) {
            return '<a href="' . url('savings/charge/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $charge_types = SavingsChargeType::orderBy('id')->get();
        $charge_options = SavingsChargeOption::orderBy('id')->get();
        $currencies = Currency::orderBy('name')->get();
        return theme_view('savings::savings_charge.create', compact('charge_options', 'charge_types', 'currencies'));
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
            'savings_charge_option_id' => ['required'],
            'savings_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'allow_override' => ['required'],
        ]);
        $savings_charge = new SavingsCharge();
        $savings_charge->created_by_id = Auth::id();
        $savings_charge->currency_id = $request->currency_id;
        $savings_charge->savings_charge_type_id = $request->savings_charge_type_id;
        $savings_charge->savings_charge_option_id = $request->savings_charge_option_id;
        $savings_charge->name = $request->name;
        $savings_charge->amount = $request->amount;
        $savings_charge->active = $request->active;
        $savings_charge->allow_override = $request->allow_override;
        $savings_charge->save();
        activity()->on($savings_charge)
            ->withProperties(['id' => $savings_charge->id])
            ->log('Create Savings Charge');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('savings/charge');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('savings::savings_charge.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $savings_charge = SavingsCharge::find($id);
        $charge_types = SavingsChargeType::orderBy('id')->get();
        $charge_options = SavingsChargeOption::orderBy('id')->get();
        $currencies = Currency::orderBy('name')->get();
        return theme_view('savings::savings_charge.edit', compact('savings_charge', 'charge_options', 'charge_types', 'currencies'));
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
            'savings_charge_option_id' => ['required'],
            'savings_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'allow_override' => ['required'],
        ]);
        $savings_charge = SavingsCharge::find($id);
        $savings_charge->currency_id = $request->currency_id;
        $savings_charge->savings_charge_type_id = $request->savings_charge_type_id;
        $savings_charge->savings_charge_option_id = $request->savings_charge_option_id;
        $savings_charge->name = $request->name;
        $savings_charge->amount = $request->amount;
        $savings_charge->active = $request->active;
        $savings_charge->allow_override = $request->allow_override;
        $savings_charge->save();
        activity()->on($savings_charge)
            ->withProperties(['id' => $savings_charge->id])
            ->log('Update Savings Charge');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('savings/charge');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $savings_charge = SavingsCharge::find($id);
        $savings_charge->delete();
        activity()->on($savings_charge)
            ->withProperties(['id' => $savings_charge->id])
            ->log('Delete Savings Charge');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
