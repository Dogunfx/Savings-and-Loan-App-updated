<?php

namespace Modules\User\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\CustomField\Entities\CustomField;
use Modules\User\Entities\User;
use Modules\User\Transformers\UsersListCollection;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:user.users.index'])->only(['index', 'show', 'get_users']);
        $this->middleware(['permission:user.users.create'])->only(['create', 'store']);
        $this->middleware(['permission:user.users.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:user.users.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = User::paginate($limit);
        return response()->json([$data]);
    }

    public function get_users(Request $request)
    {
        if ($request->limit) {
            $limit = $request->limit;
        } else {
            $limit = '';
        }
        if ($request->offset) {
            $offset = $request->offset;
        } else {
            $offset = '';
        }
        $data = User::when($offset, function ($query) use ($offset) {
            $query->offest($offset);
        })->when($limit, function ($query) use ($limit) {
            $query->limit($limit);
        })->get();
        return new UsersListCollection($data);
    }

    public function get_custom_fields()
    {
        $custom_fields = CustomField::where('category', 'add_user')->where('active', 1)->get();
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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'phone' => ['numeric'],
            'roles' => ['required','array'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $credentials = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                'notes' => $request->notes,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'email_verified_at' => date("Y-m-d H:i:s")
            ];
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads');
                $credentials['photo'] = basename($file_name);
            }
            $user = User::create($credentials);
            //attach client role
            $user->syncRoles($request->roles);
            custom_fields_save_form('add_user', $request, $user->id);
            //if client, add client to database
            return response()->json(['data' => $user, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_user')->where('active', 1)->get(), $user);
        $user->custom_fields = $custom_fields;
        return response()->json(['data' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_user')->where('active', 1)->get(), $user);
        $user->custom_fields = $custom_fields;
        return response()->json(['data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $validator = Validator::make($request->all(), [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'phone' => ['numeric'],
            'roles' => ['required','array'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $credentials = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                'notes' => $request->notes,
                'address' => $request->address,
            ];
            if (!empty($request->password)) {
                $credentials['password'] = Hash::make($request->password);
            }
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads');
                if ($user->photo) {
                    Storage::delete('public/uploads/' . $user->photo);
                }
                $credentials['photo'] = basename($file_name);
            }
            $user->syncRoles($request->roles);
            $user->update($credentials);
            custom_fields_save_form('add_user', $request, $user->id);
            return response()->json(['data' => $user, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    public function edit_profile()
    {
        $user = Auth::user();
        return response()->json(['data' => $user]);
    }


    public function update_profile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,' . $user->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $credentials = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'phone' => $request->phone,
                'gender' => $request->gender,

            ];
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads');
                if ($user->photo) {
                    Storage::delete('public/uploads/' . $user->photo);
                }
                $credentials['photo'] = basename($file_name);
            }
            if (!empty($request->password)) {
                $credentials['password'] = Hash::make($request->password);
            }

            $user->update($credentials);
            return response()->json(['data' => $user, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }
    public function get_user_clients()
    {
        return response()->json(["data" => auth()->user()], 200);
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
