<?php

namespace Modules\Branch\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchUser;
use Modules\Core\Entities\Currency;
use Modules\CustomField\Entities\CustomField;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    /**
     * BranchController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:branch.branches.index'])->only(['index', 'show']);
        $this->middleware(['permission:branch.branches.create'])->only(['create', 'store']);
        $this->middleware(['permission:branch.branches.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:branch.branches.destroy'])->only(['destroy']);
        $this->middleware(['permission:branch.branches.assign_user'])->only(['assign_user']);

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
        $data = Branch::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('branch::branch.index', compact('data'));
    }

    public function get_branches(Request $request)
    {
        $query = Branch::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('branch.branches.edit')) {
                $action .= '<li><a href="' . url('branch/' . $data->id . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('branch.branches.edit')) {
                $action .= '<li><a href="' . url('branch/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('branch.branches.destroy')) {
                $action .= '<li><a href="' . url('branch/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('branch/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('name', function ($data) {
            return '<a href="' . url('branch/' . $data->id . '/show') . '">' . $data->name . '</a>';

        })->rawColumns(['id', 'name', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $custom_fields = CustomField::where('category', 'add_branch')->where('active', 1)->get();
        return theme_view('branch::branch.create', compact('custom_fields'));
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
            'active' => ['required'],
        ]);
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->open_date = $request->open_date;
        $branch->active = $request->active;
        $branch->notes = $request->notes;
        $branch->save();
        custom_fields_save_form('add_branch', $request, $branch->id);
        activity()->on($branch)
            ->withProperties(['id' => $branch->id])
            ->log('Create Branch');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('branch');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $branch = Branch::with('users')->find($id);
        $custom_fields = CustomField::where('category', 'add_branch')->where('active', 1)->get();
        return theme_view('branch::branch.show', compact('branch', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $branch = Branch::find($id);
        $custom_fields = CustomField::where('category', 'add_branch')->where('active', 1)->get();
        return theme_view('branch::branch.edit', compact('branch', 'custom_fields'));
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
            'active' => ['required'],
        ]);
        $branch = Branch::find($id);
        $branch->name = $request->name;
        $branch->open_date = $request->open_date;
        $branch->active = $request->active;
        $branch->notes = $request->notes;
        $branch->save();
        custom_fields_save_form('add_branch', $request, $branch->id);
        activity()->on($branch)
            ->withProperties(['id' => $branch->id])
            ->log('Update Branch');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('branch');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $branch = Branch::find($id);
        if ($branch->is_system == 1) {
            \flash(trans_choice("core::general.cannot_delete_system_branch", 1))->error()->important();
            return redirect()->back();
        }
        $branch->delete();
        activity()->on($branch)
            ->withProperties(['id' => $branch->id])
            ->log('Delete Branch');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }

    public function add_user(Request $request, $id)
    {
        if (BranchUser::where('user_id', $request->user_id)->where('branch_id', $id)->get()->count() > 0) {
            Flash::warning(trans_choice("branch::general.user_already_added_to_branch", 1));
            return redirect()->back();
        }
        $branch_user = new BranchUser();
        $branch_user->branch_id = $id;
        $branch_user->user_id = $request->user_id;
        $branch_user->created_by_id = Auth::id();
        $branch_user->save();
        activity()->on($branch_user)
            ->withProperties(['id' => $branch_user->id])
            ->log('Add Branch User');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function remove_user($id)
    {
        BranchUser::destroy($id);
        activity()->log('Remove Branch User');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
