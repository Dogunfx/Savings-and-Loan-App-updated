<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\TaxRate;

class TaxRateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:core.tax_rates.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.tax_rates.create'])->only(['create', 'store']);
        $this->middleware(['permission:core.tax_rates.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:core.tax_rates.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = TaxRate::get();
        return theme_view('core::tax_rate.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('core::tax_rate.create');
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
        $tax_rate = new TaxRate();
        $tax_rate->name = $request->name;
        $tax_rate->description = $request->description;
        $tax_rate->amount = $request->amount;
        $tax_rate->code = $request->code;
        $tax_rate->active = $request->active;
        $tax_rate->type = $request->type;
        $tax_rate->save();
        activity()->on($tax_rate)
            ->withProperties(['id' => $tax_rate->id])
            ->log('Create Tax Rate');
        Flash::success(trans_choice("core::general.successfully_saved", 1));
        return redirect('tax_rate');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $tax_rate = TaxRate::find($id);
        return theme_view('core::tax_rate.show', compact('tax_rate'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $tax_rate = TaxRate::find($id);
        return theme_view('core::tax_rate.edit', compact('tax_rate'));
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
        $tax_rate = TaxRate::find($id);
        $tax_rate->name = $request->name;
        $tax_rate->description = $request->description;
        $tax_rate->amount = $request->amount;
        $tax_rate->code = $request->code;
        $tax_rate->active = $request->active;
        $tax_rate->type = $request->type;
        $tax_rate->save();
        activity()->on($tax_rate)
            ->withProperties(['id' => $tax_rate->id])
            ->log('Update Tax Rate');
        Flash::success(trans_choice("core::general.successfully_saved", 1));
        return redirect('tax_rate');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $tax_rate = TaxRate::find($id);
        $tax_rate->delete();
        activity()->on($tax_rate)
            ->withProperties(['id' => $tax_rate->id])
            ->log('Delete Tax Rate');
        Flash::success(trans_choice("core::general.successfully_deleted", 1));
        return redirect()->back();
    }
}
