<?php

namespace Modules\User\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:user.roles.index'])->only(['index', 'show', 'get_users']);
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
        $limit = $request->limit ? $request->limit : 20;
        $data = Role::paginate($limit);
        return response()->json([$data]);
    }

    public function get_permissions(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = Permission::get()->groupBy('module');
        return response()->json([$data]);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $role = Role::create(["name" => $request->name, 'guard_name' => 'web']);
            if (!$request->permissions) {
                $request->permissions = [];
            }
            foreach ($request->permissions as $key) {
                $permission = Permission::findById($key, 'web');
                $role->givePermissionTo($permission);
            }
            return response()->json(['data' => $role, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::findById($id, 'web');
        return response()->json(['data' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $role = Role::findById($id, 'web');
        if ($role->is_system == 1) {
            return response()->json(["success" => false, "message" => trans_choice("user::general.cannot_edit_system_role", 1)]);
        }
        return response()->json(['data' => $role]);

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $role = Role::findById($id, 'web');
            $role->update(["name" => $request->name]);
            $permissions = [];
            foreach ($role->getAllPermissions() as $key) {
                $permissions[$key->id] = $key->name;
            }
            if (!$request->permissions) {
                $request->permissions = [];
            }
            foreach ($request->permissions as $key) {
                $permission = Permission::findById($key);
                $role->givePermissionTo($permission);
                if (array_key_exists($key, $permissions)) {
                    unset($permissions[$key]);
                }
            }
            foreach ($permissions as $key => $value) {
                $permission = Permission::findById($key, 'web');
                $role->revokePermissionTo($permission);
            }
            return response()->json(['data' => $role, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $role = Role::findById($id, 'web');
        if ($role->is_system == 1) {
            return response()->json(["success" => false, "message" => trans_choice("user::general.cannot_delete_system_role", 1)]);
        }
        Role::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
