<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laracasts\Flash\Flash;
use Modules\Payroll\Entities\PayrollItem;

class PayrollItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:payroll.payroll.items.index'])->only(['index', 'show']);
        $this->middleware(['permission:payroll.payroll.items.create'])->only(['create', 'store']);
        $this->middleware(['permission:payroll.payroll.items.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:payroll.payroll.items.destroy'])->only(['destroy']);

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
        $data = PayrollItem::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('payroll::item.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('payroll::item.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $payroll_item = new PayrollItem();
        $payroll_item->name = $request->name;
        $payroll_item->type = $request->type;
        $payroll_item->amount_type = $request->amount_type;
        $payroll_item->amount = $request->amount;
        $payroll_item->description = $request->description;
        $payroll_item->save();
        activity()->on($payroll_item)
            ->withProperties(['id' => $payroll_item->id])
            ->log('Create Payroll Item');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payroll/item');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $payroll_item = PayrollItem::find($id);
        return theme_view('payroll::item.show', compact('payroll_item'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $payroll_item = PayrollItem::find($id);
        return theme_view('payroll::item.edit', compact('payroll_item'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $payroll_item = PayrollItem::find($id);
        $payroll_item->name = $request->name;
        $payroll_item->type = $request->type;
        $payroll_item->amount_type = $request->amount_type;
        $payroll_item->amount = $request->amount;
        $payroll_item->description = $request->description;
        $payroll_item->save();
        activity()->on($payroll_item)
            ->withProperties(['id' => $payroll_item->id])
            ->log('Update Payroll Item');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payroll/item');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $payroll_item = PayrollItem::find($id);
        $payroll_item->delete();
        activity()->on($payroll_item)
            ->withProperties(['id' => $payroll_item->id])
            ->log('Delete Payroll Item');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
