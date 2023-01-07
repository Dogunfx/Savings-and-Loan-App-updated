<?php

namespace Modules\Client\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientIdentificationType;
use Modules\Client\Entities\ClientType;

class ClientIdentificationTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:client.clients.identification_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.identification_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.identification_types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.identification_types.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = ClientIdentificationType::paginate($limit);
        return response()->json([$data]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('client::client_identification_type.create');
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
            $client_identification_type = new ClientIdentificationType();
            $client_identification_type->name = $request->name;
            $client_identification_type->save();
            return response()->json(['data' => $client_identification_type, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_identification_type = ClientIdentificationType::find($id);
        return response()->json(['data' => $client_identification_type]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_identification_type = ClientIdentificationType::find($id);
        return theme_view('client::client_identification_type.edit', compact('client_identification_type'));
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
            $client_identification_type = ClientIdentificationType::find($id);
            $client_identification_type->name = $request->name;
            $client_identification_type->save();
            return response()->json(['data' => $client_identification_type, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        ClientIdentificationType::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
