<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Profession;
use Modules\Core\Entities\PaymentType;
use Modules\Setting\Entities\Setting;
use Yajra\DataTables\Facades\DataTables;

class PaymentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:core.payment_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.payment_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:core.payment_types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:core.payment_types.destroy'])->only(['destroy']);

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
        $data = PaymentType::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('core::payment_type.index', compact('data'));
    }

    public function get_payment_types(Request $request)
    {
        $query = PaymentType::orderBy('position');
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('core.payment_types.edit')) {
                $action .= '<li><a href="' . url('payment_type/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('core.payment_types.destroy')) {
                $action .= '<li><a href="' . url('payment_type/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('payment_type/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('active', function ($data) {
            if ($data->active == 1) {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->active == 0) {
                return trans_choice('core::general.no', 1);
            }

        })->editColumn('is_cash', function ($data) {
            if ($data->is_cash == 1) {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->is_cash == 0) {
                return trans_choice('core::general.no', 1);
            }

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('core::payment_type.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'active' => ['required']
        ]);
        $payment_type = new PaymentType();
        $payment_type->name = $request->name;
        $payment_type->description = $request->description;
        $payment_type->is_cash = $request->is_cash;
        $payment_type->active = $request->active;
        $payment_type->position = $request->position;
        $payment_type->save();
        activity()->on($payment_type)
            ->withProperties(['id' => $payment_type->id])
            ->log('Create Payment Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payment_type');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $payment_type = PaymentType::find($id);
        return theme_view('core::payment_type.show', compact('payment_type'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $payment_type = PaymentType::find($id);
        return theme_view('core::payment_type.edit', compact('payment_type'));
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
            'name' => ['required'],
            'active' => ['required']
        ]);
        $payment_type = PaymentType::find($id);
        $payment_type->name = $request->name;
        $payment_type->description = $request->description;
        $payment_type->is_cash = $request->is_cash;
        $payment_type->active = $request->active;
        $payment_type->position = $request->position;
        $payment_type->save();
        activity()->on($payment_type)
            ->withProperties(['id' => $payment_type->id])
            ->log('Update Payment Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payment_type');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $payment_type = PaymentType::find($id);
        if ($payment_type->is_online) {
            Setting::where('setting_key', 'like', '%' . $payment_type->name . '.%')->delete();
            Artisan::call('module:migrate-rollback ' . $payment_type->name);
        }
        $payment_type->delete();
        activity()->on($payment_type)
            ->withProperties(['id' => $payment_type->id])
            ->log('Delete Payment Type');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }

    public function get_payment_gateway(Request $request)
    {
        $payment_type = PaymentType::find($request->id);
        $class = 'Modules\\' . $payment_type->name . '\\' . $payment_type->name;
        if (class_exists($class)) {
            $gateway_class = new $class($request->loan_id, 'loan');
            return response()->json($gateway_class->getHtml(), 200, [], JSON_UNESCAPED_SLASHES);
        } else {
            response()->json(['message' => 'Class not found', 'success' => false], 422);
        }

    }
}
