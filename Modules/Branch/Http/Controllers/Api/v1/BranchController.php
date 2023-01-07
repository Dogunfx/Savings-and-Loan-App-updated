<?php

namespace Modules\Branch\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchUser;
use Modules\CustomField\Entities\CustomField;

class BranchController extends Controller
{
    /**
     * BranchController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
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
        $limit = $request->limit ? $request->limit : 20;
        $data = Branch::paginate($limit);
        return response()->json([$data]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function get_custom_fields()
    {
        $custom_fields = CustomField::where('category', 'add_branch')->where('active', 1)->get();
        return response()->json(['data' => $custom_fields]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'active' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false,"errors" => $validator->errors()], 400);
        } else {
            $branch = new Branch();
            $branch->name = $request->name;
            $branch->open_date = $request->open_date;
            $branch->active = $request->active;
            $branch->notes = $request->notes;
            $branch->save();
            custom_fields_save_form('add_branch', $request, $branch->id);
            return response()->json(['data' => $branch, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $branch = Branch::with('users')->with('users.user')->find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_branch')->where('active', 1)->get(), $branch);
        $branch->custom_fields = $custom_fields;
        return response()->json(['data' => $branch]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $branch = Branch::find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_branch')->where('active', 1)->get(), $branch);
        $branch->custom_fields = $custom_fields;
        return response()->json(['data' => $branch]);
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
            'name' => 'required',
            'active' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false,"errors" => $validator->errors()], 400);
        } else {
            $branch = Branch::find($id);
            $branch->name = $request->name;
            $branch->open_date = $request->open_date;
            $branch->active = $request->active;
            $branch->notes = $request->notes;
            $branch->save();
            custom_fields_save_form('add_branch', $request, $branch->id);
            return response()->json(['data' => $branch, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
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
            return response()->json(["success" => false, "message" => trans_choice("core::general.cannot_delete_system_branch", 1)], 403);
        }
        $branch->delete();
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);

    }

    public function add_user(Request $request, $id)
    {
        if (BranchUser::where('user_id', $request->user_id)->where('branch_id', $id)->get()->count() > 0) {
            return response()->json(["success" => false, "message" => trans_choice("branch::general.user_already_added_to_branch", 1)], 400);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 400);
        } else {
            $branch_user = new BranchUser();
            $branch_user->branch_id = $id;
            $branch_user->user_id = $request->user_id;
            $branch_user->created_by_id = Auth::id();
            $branch_user->save();
            Flash::success(trans_choice("core::general.successfully_saved", 1));
            return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_saved", 1)]);

        }
    }

    public function remove_user($id)
    {
        BranchUser::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
