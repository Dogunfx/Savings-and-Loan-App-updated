<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Profession;
use Modules\Loan\Entities\Fund;
use Modules\Loan\Entities\LoanPurpose;
use Yajra\DataTables\Facades\DataTables;

class LoanPurposeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.purposes.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.purposes.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.purposes.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.purposes.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = LoanPurpose::paginate($limit);
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
            $loan_purpose = new LoanPurpose();
            $loan_purpose->name = $request->name;
            $loan_purpose->save();
            return response()->json(['data' => $loan_purpose, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_purpose = LoanPurpose::find($id);
        return response()->json(['data' => $loan_purpose]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_purpose = LoanPurpose::find($id);
        return response()->json(['data' => $loan_purpose]);
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
            $loan_purpose = LoanPurpose::find($id);
            $loan_purpose->name = $request->name;
            $loan_purpose->save();
            return response()->json(['data' => $loan_purpose, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        LoanPurpose::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
