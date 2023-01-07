<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanChargeOption;
use Modules\Loan\Entities\LoanChargeType;

class LoanChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.charges.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.charges.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.charges.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.charges.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = LoanCharge::paginate($limit);
        return response()->json([$data]);
    }

    public function get_charge_types()
    {
        $charge_types = LoanChargeType::orderBy('id')->get();
        return response()->json(['data' => $charge_types]);
    }

    public function get_charge_options()
    {
        $charge_options = LoanChargeOption::orderBy('id')->get();
        return response()->json(['data' => $charge_options]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_charge = new LoanCharge();
            $loan_charge->created_by_id = Auth::id();
            $loan_charge->currency_id = $request->currency_id;
            $loan_charge->loan_charge_type_id = $request->loan_charge_type_id;
            $loan_charge->loan_charge_option_id = $request->loan_charge_option_id;
            $loan_charge->name = $request->name;
            $loan_charge->amount = $request->amount;
            $loan_charge->is_penalty = $request->is_penalty;
            $loan_charge->active = $request->active;
            $loan_charge->allow_override = $request->allow_override;
            $loan_charge->save();
            return response()->json(['data' => $loan_charge, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_charge = LoanCharge::find($id);
        return response()->json(['data' => $loan_charge]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_charge = LoanCharge::find($id);
        return response()->json(['data' => $loan_charge]);
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
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_charge = LoanCharge::find($id);
            $loan_charge->currency_id = $request->currency_id;
            $loan_charge->loan_charge_type_id = $request->loan_charge_type_id;
            $loan_charge->loan_charge_option_id = $request->loan_charge_option_id;
            $loan_charge->name = $request->name;
            $loan_charge->amount = $request->amount;
            $loan_charge->is_penalty = $request->is_penalty;
            $loan_charge->active = $request->active;
            $loan_charge->allow_override = $request->allow_override;
            $loan_charge->save();
            return response()->json(['data' => $loan_charge, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        LoanCharge::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
