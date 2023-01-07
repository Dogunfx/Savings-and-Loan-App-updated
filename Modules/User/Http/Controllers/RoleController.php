<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:user.roles.index'])->only(['index', 'show', 'get_roles']);
        $this->middleware(['permission:user.roles.create'])->only(['create', 'store']);
        $this->middleware(['permission:user.roles.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:user.roles.destroy'])->only(['destroy']);

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
        $data = Role::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('user::role.index', compact('data'));
    }

    public function get_roles(Request $request)
    {


        $query = Role::query();
        return DataTables::of($query)->editColumn('user', function ($data) {
            return $data->first_name . ' ' . $data->last_name;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('user/role/' . $data->id . '/show') . '" class="">' . trans_choice('user::general.detail', 2) . '</a></li>';
            if (Auth::user()->hasPermissionTo('user.roles.edit')) {
                $action .= '<li><a href="' . url('user/role/' . $data->id . '/edit') . '" class="">' . trans_choice('user::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('user.roles.destroy')) {
                $action .= '<li><a href="' . url('user/role/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('user::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('user/role/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('name', function ($data) {
            return '<a href="' . url('user/role/' . $data->id . '/show') . '">' . $data->name . '</a>';

        })->editColumn('is_system', function ($data) {
            if ($data->is_system == "1") {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->is_system == "0") {
                return trans_choice('core::general.no', 1);
            }

        })->rawColumns(['id', 'name', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $permissions = Permission::get()->groupBy('module');
        return theme_view('user::role.create', compact('permissions'));
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
            //'guard_name' => ['required', 'string']
        ]);
        $role = Role::create(["name" => $request->name]);
        foreach ($request->permissions as $key) {
            $permission = Permission::findById($key);
            $role->givePermissionTo($permission);
        }
        activity()->on($role)
            ->withProperties(['id' => $role->id])
            ->log('Create Role');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('user/role');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::findById($id);
        $permissions = Permission::get()->groupBy('module');
        $selected_permissions = [];
        foreach ($role->getAllPermissions() as $key) {
            $selected_permissions[] = $key->id;
        }
        return theme_view('user::role.show', compact('role','permissions','selected_permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $role = Role::findById($id);
        $permissions = Permission::get()->groupBy('module');
        if ($role->is_system == 1) {
            Flash::warning(trans_choice("user::general.cannot_edit_system_role", 1));
            return redirect()->back();
        }
        $selected_permissions = [];
        foreach ($role->getAllPermissions() as $key) {
            $selected_permissions[] = $key->id;
        }
        return theme_view('user::role.edit', compact('role', 'permissions','selected_permissions'));
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
        $role = Role::findById($id);
        $role->update(["name" => $request->name]);
        $permissions = [];
        foreach ($role->getAllPermissions() as $key) {
            $permissions[$key->id] = $key->name;
        }
        foreach ($request->permissions as $key) {
            $permission = Permission::findById($key);
            $role->givePermissionTo($permission);
            if (array_key_exists($key, $permissions)) {
                unset($permissions[$key]);
            }
        }
        foreach ($permissions as $key => $value) {
            $permission = Permission::findById($key);
            $role->revokePermissionTo($permission);
        }
        activity()->on($role)
            ->withProperties(['id' => $role->id])
            ->log('Update Role');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('user/role');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $role = Role::findById($id);
        if ($role->is_system == 1) {
            Flash::warning(trans_choice("user::general.cannot_delete_system_role", 1));
            return redirect()->back();
        }
        $role->delete();
        activity()->on($role)
            ->withProperties(['id' => $role->id])
            ->log('Delete Role');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
