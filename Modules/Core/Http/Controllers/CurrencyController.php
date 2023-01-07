<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\PaymentType;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','2fa']);
        $this->middleware(['permission:core.currencies.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.currencies.create'])->only(['create', 'store']);
        $this->middleware(['permission:core.currencies.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:core.currencies.destroy'])->only(['destroy']);

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
        $data = Currency::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('core::currency.index',compact('data'));
    }

    public function get_currencies(Request $request)
    {
        $query = Currency::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('core.currencies.edit')) {
                $action .= '<li><a href="' . url('currency/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('core.currencies.destroy')) {
                $action .= '<li><a href="' . url('currency/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('currency/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('core::currency.create');
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
            'symbol' => ['required']
        ]);
        $currency = new Currency();
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->position = $request->position;
        $currency->save();
        activity()->on($currency)
            ->withProperties(['id' => $currency->id])
            ->log('Create Currency');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('currency');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $currency = Currency::find($id);
        return theme_view('core::currency.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $currency = Currency::find($id);
        return theme_view('core::currency.edit', compact('currency'));
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
            'symbol' => ['required']
        ]);
        $currency = Currency::find($id);
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->position = $request->position;
        $currency->save();
        activity()->on($currency)
            ->withProperties(['id' => $currency->id])
            ->log('Update Currency');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('currency');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $currency = Currency::find($id);
        $currency->delete();
        activity()->on($currency)
            ->withProperties(['id' => $currency->id])
            ->log('Delete Currency');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
