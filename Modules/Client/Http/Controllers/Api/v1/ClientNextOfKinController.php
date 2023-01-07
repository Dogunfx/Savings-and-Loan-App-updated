<?php

namespace Modules\Client\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientNextOfKin;
use Modules\Client\Entities\ClientRelationship;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;

class ClientNextOfKinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:client.clients.next_of_kin.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.next_of_kin.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.next_of_kin.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.next_of_kin.destroy'])->only(['destroy']);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'mobile' => ['numeric'],
            'client_relationship_id' => ['required'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => ['date'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_next_of_kin = new ClientNextOfKin();
            $client_next_of_kin->first_name = $request->first_name;
            $client_next_of_kin->last_name = $request->last_name;
            $client_next_of_kin->client_id = $id;
            $client_next_of_kin->created_by_id = Auth::id();
            $client_next_of_kin->gender = $request->gender;
            $client_next_of_kin->country_id = $request->country_id;
            $client_next_of_kin->client_relationship_id = $request->client_relationship_id;
            $client_next_of_kin->title_id = $request->title_id;
            $client_next_of_kin->profession_id = $request->profession_id;
            $client_next_of_kin->mobile = $request->mobile;
            $client_next_of_kin->notes = $request->notes;
            $client_next_of_kin->email = $request->email;
            $client_next_of_kin->address = $request->address;
            $request->dob ? $client_next_of_kin->dob = $request->dob : '';
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/clients');
                $client_next_of_kin->photo = basename($file_name);
            }
            $client_next_of_kin->save();
            return response()->json(['data' => $client_next_of_kin, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_next_of_kin = ClientNextOfKin::find($id);
        return response()->json(['data' => $client_next_of_kin]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {

        $client_next_of_kin = ClientNextOfKin::find($id);
        return response()->json(['data' => $client_next_of_kin]);
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
            'mobile' => ['numeric'],
            'client_relationship_id' => ['required'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => ['date'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_next_of_kin = ClientNextOfKin::find($id);
            $client_next_of_kin->first_name = $request->first_name;
            $client_next_of_kin->last_name = $request->last_name;
            $client_next_of_kin->gender = $request->gender;
            $client_next_of_kin->country_id = $request->country_id;
            $client_next_of_kin->client_relationship_id = $request->client_relationship_id;
            $client_next_of_kin->title_id = $request->title_id;
            $client_next_of_kin->profession_id = $request->profession_id;
            $client_next_of_kin->mobile = $request->mobile;
            $client_next_of_kin->notes = $request->notes;
            $client_next_of_kin->email = $request->email;
            $client_next_of_kin->address = $request->address;
            $request->dob ? $client_next_of_kin->dob = $request->dob : '';
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/clients');
                if ($client_next_of_kin->photo) {
                    Storage::delete('public/uploads/clients/' . $client_next_of_kin->photo);
                }
                $client_next_of_kin->photo = basename($file_name);
            }
            $client_next_of_kin->save();
            return response()->json(['data' => $client_next_of_kin, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        ClientNextOfKin::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
