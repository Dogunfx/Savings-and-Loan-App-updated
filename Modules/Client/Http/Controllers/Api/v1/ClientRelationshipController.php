<?php

namespace Modules\Client\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientRelationship;
use Modules\Client\Entities\ClientType;
use Yajra\DataTables\Facades\DataTables;

class ClientRelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:client.clients.client_relationships.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.client_relationships.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.client_relationships.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.client_relationships.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = ClientRelationship::paginate($limit);
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
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_relationship = new ClientRelationship();
            $client_relationship->name = $request->name;
            $client_relationship->save();
            return response()->json(['data' => $client_relationship, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_relationship = ClientRelationship::find($id);
        return response()->json(['data' => $client_relationship]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_relationship = ClientRelationship::find($id);
        return response()->json(['data' => $client_relationship]);
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
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_relationship = ClientRelationship::find($id);
            $client_relationship->name = $request->name;
            $client_relationship->save();
            return response()->json(['data' => $client_relationship, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        ClientRelationship::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
