<?php

namespace Modules\Client\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientType;
use Modules\Client\Entities\ClientUser;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;
use Modules\CustomField\Entities\CustomField;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:client.clients.index'])->only(['index', 'show', 'get_clients']);
        $this->middleware(['permission:client.clients.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.destroy'])->only(['destroy']);
        $this->middleware(['permission:client.clients.user.create'])->only(['store_user', 'create_user']);
        $this->middleware(['permission:client.clients.user.destroy'])->only(['destroy_user']);
        $this->middleware(['permission:client.clients.activate'])->only(['change_status']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $status = $request->status;
        $branch_id = $request->branch_id;
        $limit = $request->limit ? $request->limit : 20;
        $data = DB::table("clients")
            ->leftJoin("branches", "branches.id", "clients.branch_id")
            ->leftJoin("users", "users.id", "clients.loan_officer_id")
            ->selectRaw("branches.name branch,concat(users.first_name,' ',users.last_name) staff,clients.id,clients.loan_officer_id,concat(clients.first_name,' ',clients.last_name) name,clients.gender,clients.mobile,clients.email,clients.external_id,clients.status")
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })->paginate($limit);
        return response()->json([$data]);
    }

    public function get_custom_fields()
    {
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
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
            'branch_id' => ['required'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => ['required', 'date'],
            'created_date' => ['required', 'date'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client = new Client();
            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->external_id = $request->external_id;
            $client->created_by_id = Auth::id();
            $client->gender = $request->gender;
            $client->country_id = $request->country_id;
            $client->loan_officer_id = $request->loan_officer_id;
            $client->title_id = $request->title_id;
            $client->branch_id = $request->branch_id;
            $client->client_type_id = $request->client_type_id;
            $client->profession_id = $request->profession_id;
            $client->mobile = $request->mobile;
            $client->notes = $request->notes;
            $client->email = $request->email;
            $client->address = $request->address;
            $client->marital_status = $request->marital_status;
            $client->created_date = $request->created_date;
            $request->dob ? $client->dob = $request->dob : '';
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/clients');
                $client->photo = basename($file_name);
            }
            $client->save();
            custom_fields_save_form('add_client', $request, $client->id);
            return response()->json(['data' => $client, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client = Client::with('client_users')->with('client_users.user')->with('identifications')->with('identifications.identification_type')->with('files')->with('next_of_kins')->with('next_of_kins.next_of_kins')->with('next_of_kins.client_relationship')->find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_client')->where('active', 1)->get(), $client);
        $client->custom_fields = $custom_fields;
        return response()->json(['data' => $client]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client = Client::find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_client')->where('active', 1)->get(), $client);
        $client->custom_fields = $custom_fields;
        return response()->json(['data' => $client]);
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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => ['required', 'date'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client = Client::find($id);
            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->external_id = $request->external_id;
            $client->gender = $request->gender;
            $client->country_id = $request->country_id;
            $client->loan_officer_id = $request->loan_officer_id;
            $client->title_id = $request->title_id;
            $client->client_type_id = $request->client_type_id;
            $client->profession_id = $request->profession_id;
            $client->mobile = $request->mobile;
            $client->notes = $request->notes;
            $client->email = $request->email;
            $client->address = $request->address;
            $client->marital_status = $request->marital_status;
            $request->dob ? $client->dob = $request->dob : '';
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/clients');
                //check if we had a file before
                if ($client->photo) {
                    Storage::delete('public/uploads/clients/' . $client->photo);
                }
                $client->photo = basename($file_name);
            }
            $client->save();
            custom_fields_save_form('add_client', $request, $client->id);
            return response()->json(['data' => $client, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        Client::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);

    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store_user(Request $request, $id)
    {
        if ($request->existing == 1) {

            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(["success" => false, "errors" => $validator->errors()], 400);
            } else {
                if (ClientUser::where('client_id', $id)->where('user_id', $request->user_id)->get()->count() > 0) {
                    return response()->json(["success" => true, "message" => trans_choice("client::general.user_already_added", 1)]);
                }
                $client_user = new ClientUser();
                $client_user->client_id = $id;
                $client_user->created_by_id = Auth::id();
                $client_user->user_id = $request->user_id;
                $client_user->save();
                return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_saved", 1)]);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'first_name' => ['required'],
                'last_name' => ['required'],
                'gender' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
            ]);
            if ($validator->fails()) {
                return response()->json(["success" => false, "errors" => $validator->errors()], 400);
            } else {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'notes' => $request->notes,
                    'address' => $request->address,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => date("Y-m-d H:i:s")
                ]);
                //attach client role
                $role = Role::findByName('client');
                $user->assignRole($role);
                $client_user = new ClientUser();
                $client_user->client_id = $id;
                $client_user->created_by_id = Auth::id();
                $client_user->user_id = $user->id;
                $client_user->save();
                return response()->json(["success" => true, "data" => $user, "message" => trans_choice("core::general.successfully_saved", 1)]);
            }

        }

    }

    public function destroy_user($id)
    {
        ClientUser::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_saved", 1)]);

    }

    public function change_status(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required'],
            'date' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client = Client::find($id);
            $client->status = $request->status;
            $client->save();
            return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_saved", 1)]);
        }
    }

}
