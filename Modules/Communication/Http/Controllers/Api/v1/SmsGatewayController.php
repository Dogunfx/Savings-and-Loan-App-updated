<?php

namespace Modules\Communication\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Communication\Entities\SmsGateway;

class SmsGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:communication.sms_gateways.index'])->only(['index', 'show']);
        $this->middleware(['permission:communication.sms_gateways.create'])->only(['create', 'store']);
        $this->middleware(['permission:communication.sms_gateways.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:communication.sms_gateways.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = SmsGateway::paginate($limit);
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
            'to_name' => ['required'],
            'msg_name' => ['required'],
            'url' => ['required'],
            'active' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $sms_gateway = new SmsGateway();
            $sms_gateway->created_by_id = Auth::id();
            $sms_gateway->name = $request->name;
            $sms_gateway->to_name = $request->to_name;
            $sms_gateway->msg_name = $request->msg_name;
            $sms_gateway->url = $request->url;
            $sms_gateway->active = $request->active;
            $sms_gateway->save();
            return response()->json(['data' => $sms_gateway, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $sms_gateway = SmsGateway::find($id);
        return response()->json(['data' => $sms_gateway]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $sms_gateway = SmsGateway::find($id);
        return response()->json(['data' => $sms_gateway]);
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
            'to_name' => ['required'],
            'msg_name' => ['required'],
            'url' => ['required'],
            'active' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $sms_gateway = SmsGateway::find($id);
            $sms_gateway->name = $request->name;
            $sms_gateway->to_name = $request->to_name;
            $sms_gateway->msg_name = $request->msg_name;
            $sms_gateway->url = $request->url;
            $sms_gateway->active = $request->active;
            $sms_gateway->save();
            return response()->json(['data' => $sms_gateway, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        SmsGateway::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
