<?php

namespace Modules\Core\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\PaymentType;

class PaymentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:core.payment_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.payment_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:core.payment_types.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:core.payment_types.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = PaymentType::orderBy('position')->paginate($limit);
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
            'active' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $payment_type = new PaymentType();
            $payment_type->name = $request->name;
            $payment_type->description = $request->description;
            $payment_type->is_cash = $request->is_cash;
            $payment_type->active = $request->active;
            $payment_type->position = $request->position;
            $payment_type->save();
            return response()->json(['data' => $payment_type, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $payment_type = PaymentType::find($id);
        return response()->json(['data' => $payment_type]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $payment_type = PaymentType::find($id);
        return response()->json(['data' => $payment_type]);
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
            'active' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $payment_type = PaymentType::find($id);
            $payment_type->name = $request->name;
            $payment_type->description = $request->description;
            $payment_type->is_cash = $request->is_cash;
            $payment_type->active = $request->active;
            $payment_type->position = $request->position;
            $payment_type->save();
            return response()->json(['data' => $payment_type, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        PaymentType::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
