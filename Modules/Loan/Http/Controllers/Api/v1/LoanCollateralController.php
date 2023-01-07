<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\CustomField\Entities\CustomField;
use Modules\Loan\Entities\LoanCollateral;

class LoanCollateralController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.collateral.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.collateral.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.collateral.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.collateral.destroy'])->only(['destroy']);

    }

    public function get_custom_fields()
    {
        $custom_fields = CustomField::where('category', 'add_collateral')->where('active', 1)->get();
        return response()->json(['data' => $custom_fields]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'loan_collateral_type_id' => ['required'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_collateral = new LoanCollateral();
            $loan_collateral->created_by_id = Auth::id();
            $loan_collateral->loan_id = $id;
            $loan_collateral->loan_collateral_type_id = $request->loan_collateral_type_id;
            $loan_collateral->value = $request->value;
            $loan_collateral->description = $request->description;
            if ($request->hasFile('file')) {
                $file_name = $request->file('file')->store('public/uploads/loans');
                $loan_collateral->link = basename($file_name);
            }
            $loan_collateral->save();
            return response()->json(['data' => $loan_collateral, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {

        $loan_collateral = LoanCollateral::find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_collateral')->where('active', 1)->get(), $loan_collateral);
        $loan_collateral->custom_fields = $custom_fields;
        return response()->json(['data' => $loan_collateral]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_collateral = LoanCollateral::find($id);
        $custom_fields = custom_fields_build_data_for_json(CustomField::where('category', 'add_collateral')->where('active', 1)->get(), $loan_collateral);
        $loan_collateral->custom_fields = $custom_fields;
        return response()->json(['data' => $loan_collateral]);
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
            'loan_collateral_type_id' => ['required'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_collateral = LoanCollateral::find($id);
            $loan_collateral->loan_collateral_type_id = $request->loan_collateral_type_id;
            $loan_collateral->value = $request->value;
            $loan_collateral->description = $request->description;
            if ($request->hasFile('file')) {
                $file_name = $request->file('file')->store('public/uploads/loans');
                $loan_collateral->link = basename($file_name);
            }
            $loan_collateral->save();
            return response()->json(['data' => $loan_collateral, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_collateral = LoanCollateral::find($id);
        if ($loan_collateral->link) {
            Storage::delete('public/uploads/loans/' . $loan_collateral->link);
        }
        $loan_collateral->delete();
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
