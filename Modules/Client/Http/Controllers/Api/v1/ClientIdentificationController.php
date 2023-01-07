<?php

namespace Modules\Client\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientIdentification;
use Modules\Client\Entities\ClientIdentificationType;

class ClientIdentificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:client.clients.identification.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.identification.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.identification.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.identification.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('client::client_identification.index');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'client_identification_type_id' => ['required'],
            'identification_value' => ['required'],
            'photo' => ['file', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_identification = new ClientIdentification();
            $client_identification->created_by_id = Auth::id();
            $client_identification->client_id = $id;
            $client_identification->identification_value = $request->identification_value;
            $client_identification->client_identification_type_id = $request->client_identification_type_id;
            $client_identification->description = $request->description;
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/clients');
                $client_identification->link = basename($file_name);
            }
            $client_identification->save();
            return response()->json(['data' => $client_identification, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_identification = ClientIdentification::find($id);
        return response()->json(['data' => $client_identification]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_identification = ClientIdentification::find($id);
        return response()->json(['data' => $client_identification]);
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
            'client_identification_type_id' => ['required'],
            'identification_value' => ['required'],
            'photo' => ['file', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_identification = ClientIdentification::find($id);
            $client_identification->identification_value = $request->identification_value;
            $client_identification->client_identification_type_id = $request->client_identification_type_id;
            $client_identification->description = $request->description;
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/clients');
                if ($client_identification->link) {
                    Storage::delete('public/uploads/clients/' . $client_identification->link);
                }
                $client_identification->link = basename($file_name);
            }
            $client_identification->save();
            return response()->json(['data' => $client_identification, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        ClientIdentification::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
