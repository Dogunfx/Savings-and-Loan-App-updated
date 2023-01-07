<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Loan\Entities\Fund;

class FundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.funds.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.funds.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.funds.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.funds.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = Fund::paginate($limit);
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
            $fund = new Fund();
            $fund->name = $request->name;
            $fund->save();
            return response()->json(['data' => $fund, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $fund = Fund::find($id);
        return response()->json(['data' => $fund]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $fund = Fund::find($id);
        return response()->json(['data' => $fund]);
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
            $fund = Fund::find($id);
            $fund->name = $request->name;
            $fund->save();
            return response()->json(['data' => $fund, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        Fund::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
