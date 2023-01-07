<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetType;
use Modules\Branch\Entities\Branch;
use Modules\CustomField\Entities\CustomField;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:asset.assets.index'])->only(['index', 'show']);
        $this->middleware(['permission:asset.assets.create'])->only(['create', 'store']);
        $this->middleware(['permission:asset.assets.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:asset.assets.destroy'])->only(['destroy']);

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
        $data = Asset::leftJoin("branches", "branches.id", "assets.branch_id")
            ->leftJoin("asset_types", "asset_types.id", "assets.asset_type_id")
            ->leftJoin("users", "users.id", "assets.created_by_id")
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('assets.id', 'like', "%$search%");
                $query->orWhere('branches.name', 'like', "%$search%");
                $query->orWhere('asset_types.name', 'like', "%$search%");
            })
            ->selectRaw("assets.*,branches.name branch,asset_types.name asset_type,concat(users.first_name,' ',users.last_name) created_by")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('asset::asset.index', compact('data'));
    }

    public function get_assets(Request $request)
    {

        $farm_id = $request->farm_id;
        $asset_type_id = $request->asset_type_id;

        $query = DB::table("assets")
            ->leftJoin("branches", "branches.id", "assets.branch_id")
            ->leftJoin("asset_types", "asset_types.id", "assets.asset_type_id")
            ->leftJoin("users", "users.id", "assets.created_by_id")
            ->when($farm_id, function ($query) use ($farm_id) {
                $query->where("assets.farm_id", $farm_id);
            })
            ->when($asset_type_id, function ($query) use ($asset_type_id) {
                $query->where("assets.asset_type_id", $asset_type_id);
            })
            ->selectRaw("assets.*,branches.name branch,asset_types.name asset_type,concat(users.first_name,' ',users.last_name) created_by");
        return DataTables::of($query)->editColumn('debit', function ($data) {
            return number_format($data->purchase_price, 2);
        })->editColumn('salvage_value', function ($data) {
            return number_format($data->salvage_value, 2);
        })->editColumn('value', function ($data) {
            return number_format($data->value, 2);
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('asset/' . $data->id . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            $action .= '<li><a href="' . url('asset/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            $action .= '<li><a href="' . url('asset/' . $data->id . '/delete') . '" class="">' . trans_choice('core::general.delete', 2) . '</a></li>';

            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('asset/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $branches = Branch::all();
        $asset_types = AssetType::all();
        $custom_fields = CustomField::where('category', 'add_asset')->where('active', 1)->get();
        return theme_view('asset::asset.create', compact('branches', 'asset_types', 'custom_fields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $asset = new Asset();
        $asset->created_by_id = Auth::id();
        $asset->asset_type_id = $request->asset_type_id;
        $asset->branch_id = $request->branch_id;
        $asset->name = $request->name;
        $asset->purchase_date = $request->purchase_date;
        $asset->purchase_price = $request->purchase_price;
        $asset->value = $request->purchase_price;
        $asset->life_span = $request->life_span;
        $asset->salvage_value = $request->salvage_value;
        $asset->notes = $request->notes;
        $asset->save();
        custom_fields_save_form('add_asset', $request, $asset->id);
        activity()->on($asset)
            ->withProperties(['id' => $asset->id])
            ->log('Create Asset');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('asset');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $asset = Asset::find($id);
        $custom_fields = CustomField::where('category', 'add_asset')->where('active', 1)->get();
        return theme_view('asset::asset.show', compact('asset', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $branches = Branch::all();
        $asset_types = AssetType::all();
        $asset = Asset::find($id);
        $custom_fields = CustomField::where('category', 'add_asset')->where('active', 1)->get();
        return theme_view('asset::asset.edit', compact('branches', 'asset_types', 'asset', 'custom_fields'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::find($id);
        $asset->asset_type_id = $request->asset_type_id;
        $asset->branch_id = $request->branch_id;
        $asset->name = $request->name;
        $asset->purchase_date = $request->purchase_date;
        $asset->purchase_price = $request->purchase_price;
        $asset->value = $request->purchase_price;
        $asset->life_span = $request->life_span;
        $asset->salvage_value = $request->salvage_value;
        $asset->notes = $request->notes;
        $asset->save();
        custom_fields_save_form('add_asset', $request, $asset->id);
        activity()->on($asset)
            ->withProperties(['id' => $asset->id])
            ->log('Update Asset');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('asset');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $asset = Asset::find($id);
        $asset->delete();
        activity()->on($asset)
            ->withProperties(['id' => $asset->id])
            ->log('Delete Asset');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
