<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laracasts\Flash\Flash;
use Modules\Payroll\Entities\PayrollItem;
use Modules\Payroll\Entities\PayrollTemplate;
use Modules\Payroll\Entities\PayrollTemplateItem;

class PayrollTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:payroll.payroll.templates.index'])->only(['index', 'show']);
        $this->middleware(['permission:payroll.payroll.templates.create'])->only(['create', 'store']);
        $this->middleware(['permission:payroll.payroll.templates.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:payroll.payroll.templates.destroy'])->only(['destroy']);

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
        $data = PayrollTemplate::with('payroll_items')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('payroll::template.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $payroll_items = PayrollItem::get();
        return theme_view('payroll::template.create', compact('payroll_items'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $payroll_template = new PayrollTemplate();
        $payroll_template->name = $request->name;
        $payroll_template->description = $request->description;
        $payroll_template->work_duration = $request->work_duration;
        $payroll_template->duration_unit = $request->duration_unit;
        $payroll_template->amount_per_duration = $request->amount_per_duration;
        $payroll_template->total_duration_amount = $request->amount_per_duration * $request->work_duration;
        $payroll_template->save();
        if (!empty($request->payroll_items)) {
            foreach ($request->payroll_items as $key) {
                $payroll_template_item = new PayrollTemplateItem();
                $payroll_template_item->payroll_template_id = $payroll_template->id;
                $payroll_template_item->payroll_item_id = $key;
                $payroll_template_item->save();
            }
        }
        activity()->on($payroll_template)
            ->withProperties(['id' => $payroll_template->id])
            ->log('Create Payroll Template');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payroll/template');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('payroll::template.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $payroll_template = PayrollTemplate::with('payroll_items')->find($id);
        $payroll_items = PayrollItem::get();
        $items = [];
        foreach ($payroll_template->payroll_items as $item) {
            $items[] = $item->payroll_item_id;
        }
        return theme_view('payroll::template.edit', compact('payroll_template', 'payroll_items', 'items'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $payroll_template = PayrollTemplate::find($id);
        $payroll_template->name = $request->name;
        $payroll_template->description = $request->description;
        $payroll_template->work_duration = $request->work_duration;
        $payroll_template->duration_unit = $request->duration_unit;
        $payroll_template->amount_per_duration = $request->amount_per_duration;
        $payroll_template->total_duration_amount = $request->amount_per_duration * $request->work_duration;
        $payroll_template->save();
        PayrollTemplateItem::where('payroll_template_id', $id)->delete();
        if (!empty($request->payroll_items)) {
            foreach ($request->payroll_items as $key) {
                $payroll_template_item = new PayrollTemplateItem();
                $payroll_template_item->payroll_template_id = $payroll_template->id;
                $payroll_template_item->payroll_item_id = $key;
                $payroll_template_item->save();
            }
        }
        activity()->on($payroll_template)
            ->withProperties(['id' => $payroll_template->id])
            ->log('Update Payroll Template');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payroll/template');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $payroll_template = PayrollTemplate::find($id);
        $payroll_template->delete();
        PayrollTemplateItem::where('payroll_template_id', $id)->delete();
        activity()->on($payroll_template)
            ->withProperties(['id' => $payroll_template->id])
            ->log('Delete Payroll Template');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
