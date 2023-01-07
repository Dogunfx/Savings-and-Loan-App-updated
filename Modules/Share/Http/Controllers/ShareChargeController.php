<?php

namespace Modules\Share\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Currency;
use Modules\Share\Entities\ShareCharge;
use Modules\Share\Entities\ShareChargeOption;
use Modules\Share\Entities\ShareChargeType;
use Yajra\DataTables\Facades\DataTables;

class ShareChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:share.shares.charges.index'])->only(['index', 'show']);
        $this->middleware(['permission:share.shares.charges.create'])->only(['create', 'store']);
        $this->middleware(['permission:share.shares.charges.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:share.shares.charges.destroy'])->only(['destroy']);

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
        $data = ShareCharge::leftJoin('currencies', 'currencies.id', 'share_charges.currency_id')
            ->leftJoin('share_charge_types', 'share_charge_types.id', 'share_charges.share_charge_type_id')
            ->leftJoin('share_charge_options', 'share_charge_options.id', 'share_charges.share_charge_option_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('share_charges.name', 'like', "%$search%");
            })
            ->selectRaw("share_charges.*,currencies.name currency,share_charge_types.name charge_type,share_charge_options.name charge_option")            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('share::share_charge.index',compact('data'));
    }

    public function get_charges(Request $request)
    {
        $query = ShareCharge::leftJoin('currencies', 'currencies.id', 'share_charges.currency_id')
            ->leftJoin('share_charge_types', 'share_charge_types.id', 'share_charges.share_charge_type_id')
            ->leftJoin('share_charge_options', 'share_charge_options.id', 'share_charges.share_charge_option_id')
            ->selectRaw("share_charges.*,currencies.name currency,share_charge_types.name charge_type,share_charge_options.name charge_option");

        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('share.shares.charges.edit')) {
                $action .= '<li><a href="' . url('share/charge/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('share.shares.charges.destroy')) {
                $action .= '<li><a href="' . url('share/charge/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('charge_type', function ($data) {
            if ($data->share_charge_type_id == 1) {
                return trans_choice('share::general.share_account_activation', 1);
            }
            if ($data->share_charge_type_id == 2) {
                return trans_choice('share::general.share_purchase', 1);
            }
            if ($data->share_charge_type_id == 3) {
                return trans_choice('share::general.share_redeem', 1);
            }
        })->editColumn('charge_option', function ($data) {
            if ($data->share_charge_option_id == 1) {
                return number_format($data->amount, 2) . ' ' . trans_choice('share::general.flat', 1);
            }
            if ($data->share_charge_option_id == 2) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('share::general.percentage_of_amount', 1);
            }

        })->editColumn('active', function ($data) {
            if ($data->active == 1) {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->active == 0) {
                return trans_choice('core::general.no', 1);
            }
        })->editColumn('id', function ($data) {
            return '<a href="' . url('share/charge/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $charge_types = ShareChargeType::orderBy('id')->get();
        $charge_options = ShareChargeOption::orderBy('id')->get();
        $currencies = Currency::orderBy('name')->get();
        return theme_view('share::share_charge.create', compact('charge_options', 'charge_types', 'currencies'));
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
            'share_charge_option_id' => ['required'],
            'share_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'allow_override' => ['required'],
        ]);
        $share_charge = new ShareCharge();
        $share_charge->created_by_id = Auth::id();
        $share_charge->currency_id = $request->currency_id;
        $share_charge->share_charge_type_id = $request->share_charge_type_id;
        $share_charge->share_charge_option_id = $request->share_charge_option_id;
        $share_charge->name = $request->name;
        $share_charge->amount = $request->amount;
        $share_charge->active = $request->active;
        $share_charge->allow_override = $request->allow_override;
        $share_charge->save();
        activity()->on($share_charge)
            ->withProperties(['id' => $share_charge->id])
            ->log('Create Share Charge');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/charge');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('share::share_charge.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $share_charge = ShareCharge::find($id);
        $charge_types = ShareChargeType::orderBy('id')->get();
        $charge_options = ShareChargeOption::orderBy('id')->get();
        $currencies = Currency::orderBy('name')->get();
        return theme_view('share::share_charge.edit', compact('share_charge', 'charge_options', 'charge_types', 'currencies'));
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
            'share_charge_option_id' => ['required'],
            'share_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'allow_override' => ['required'],
        ]);
        $share_charge = ShareCharge::find($id);
        $share_charge->currency_id = $request->currency_id;
        $share_charge->share_charge_type_id = $request->share_charge_type_id;
        $share_charge->share_charge_option_id = $request->share_charge_option_id;
        $share_charge->name = $request->name;
        $share_charge->amount = $request->amount;
        $share_charge->active = $request->active;
        $share_charge->allow_override = $request->allow_override;
        $share_charge->save();
        activity()->on($share_charge)
            ->withProperties(['id' => $share_charge->id])
            ->log('Update Share Charge');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('share/charge');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $share_charge = ShareCharge::find($id);
        $share_charge->delete();
        activity()->on($share_charge)
            ->withProperties(['id' => $share_charge->id])
            ->log('Delete Share Charge');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
