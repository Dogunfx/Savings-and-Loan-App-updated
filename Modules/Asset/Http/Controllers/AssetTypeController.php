<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Asset\Entities\AssetType;
use Yajra\DataTables\Facades\DataTables;

class AssetTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:asset.assets.types.index'])->only(['index', 'show']);
        $this->middleware(['permission:asset.assets.types.create'])->only(['create', 'store']);
        $this->middleware(['permission:asset.assets.types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:asset.assets.types.destroy'])->only(['destroy']);

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
        $data = AssetType::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());

        return theme_view('asset::asset_type.index',compact('data'));
    }

    public function get_asset_types(Request $request)
    {

        $farm_id = $request->farm_id;
        $asset_type_id = $request->asset_type_id;

        $query = DB::table("asset_types")
            ->selectRaw("asset_types.*");
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('asset/type/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            $action .= '<li><a href="' . url('asset/type/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';

            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('asset/type/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $chart_of_accounts = ChartOfAccount::all();
        return theme_view('asset::asset_type.create', compact('chart_of_accounts'));
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

        ]);
        $asset_type = new AssetType();
        $asset_type->name = $request->name;
        $asset_type->chart_of_account_asset_id = $request->chart_of_account_asset_id;
        $asset_type->chart_of_account_expense_id = $request->chart_of_account_expense_id;
        $asset_type->chart_of_account_fixed_asset_id = $request->chart_of_account_fixed_asset_id;
        $asset_type->chart_of_account_contra_asset_id = $request->chart_of_account_contra_asset_id;
        $asset_type->chart_of_account_liability_id = $request->chart_of_account_liability_id;
        $asset_type->chart_of_account_income_id = $request->chart_of_account_income_id;
        $asset_type->notes = $request->notes;
        $asset_type->save();
        activity()->on($asset_type)
            ->withProperties(['id' => $asset_type->id])
            ->log('Create Asset Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('asset/type');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $asset_type = AssetType::find($id);
        return theme_view('asset::asset_type.show', compact('asset_type'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $chart_of_accounts = ChartOfAccount::all();
        $asset_type = AssetType::find($id);
        return theme_view('asset::asset_type.edit', compact('asset_type', 'chart_of_accounts'));
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

        ]);
        $asset_type = AssetType::find($id);
        $asset_type->name = $request->name;
        $asset_type->chart_of_account_asset_id = $request->chart_of_account_asset_id;
        $asset_type->chart_of_account_expense_id = $request->chart_of_account_expense_id;
        $asset_type->chart_of_account_fixed_asset_id = $request->chart_of_account_fixed_asset_id;
        $asset_type->chart_of_account_contra_asset_id = $request->chart_of_account_contra_asset_id;
        $asset_type->chart_of_account_liability_id = $request->chart_of_account_liability_id;
        $asset_type->chart_of_account_income_id = $request->chart_of_account_income_id;
        $asset_type->notes = $request->notes;
        $asset_type->save();
        activity()->on($asset_type)
            ->withProperties(['id' => $asset_type->id])
            ->log('Update Asset Type');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('asset/type');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $asset_type = AssetType::find($id);
        $asset_type->delete();
        activity()->on($asset_type)
            ->withProperties(['id' => $asset_type->id])
            ->log('Delete Asset Type');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
