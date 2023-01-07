<?php

namespace Modules\Savings\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Savings\Entities\SavingsCharge;
use Modules\Savings\Entities\SavingsChargeOption;
use Modules\Savings\Entities\SavingsChargeType;

class SavingsChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:savings.savings.charges.index'])->only(['index', 'show']);
        $this->middleware(['permission:savings.savings.charges.create'])->only(['create', 'store']);
        $this->middleware(['permission:savings.savings.charges.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:savings.savings.charges.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = SavingsCharge::leftJoin('currencies', 'currencies.id', 'savings_charges.currency_id')
            ->leftJoin('savings_charge_types', 'savings_charge_types.id', 'savings_charges.savings_charge_type_id')
            ->leftJoin('savings_charge_options', 'savings_charge_options.id', 'savings_charges.savings_charge_option_id')
            ->selectRaw("savings_charges.*,currencies.name currency,savings_charge_types.name charge_type,savings_charge_options.name charge_option")
            ->paginate($limit);
        return response()->json([$data]);
    }

    public function get_charge_types()
    {
        $charge_types = SavingsChargeType::orderBy('id')->get();
        return response()->json(['data' => $charge_types]);
    }

    public function get_charge_options()
    {
        $charge_options = SavingsChargeOption::orderBy('id')->get();
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
            'savings_charge_option_id' => ['required'],
            'savings_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'allow_override' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $savings_charge = new SavingsCharge();
            $savings_charge->created_by_id = Auth::id();
            $savings_charge->currency_id = $request->currency_id;
            $savings_charge->savings_charge_type_id = $request->savings_charge_type_id;
            $savings_charge->savings_charge_option_id = $request->savings_charge_option_id;
            $savings_charge->name = $request->name;
            $savings_charge->amount = $request->amount;
            $savings_charge->active = $request->active;
            $savings_charge->allow_override = $request->allow_override;
            $savings_charge->save();
            return response()->json(['data' => $savings_charge, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $savings_charge = SavingsCharge::find($id);
        return response()->json(['data' => $savings_charge]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $savings_charge = SavingsCharge::find($id);
        return response()->json(['data' => $savings_charge]);
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
            'savings_charge_option_id' => ['required'],
            'savings_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'allow_override' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $savings_charge = SavingsCharge::find($id);
            $savings_charge->currency_id = $request->currency_id;
            $savings_charge->savings_charge_type_id = $request->savings_charge_type_id;
            $savings_charge->savings_charge_option_id = $request->savings_charge_option_id;
            $savings_charge->name = $request->name;
            $savings_charge->amount = $request->amount;
            $savings_charge->active = $request->active;
            $savings_charge->allow_override = $request->allow_override;
            $savings_charge->save();
            return response()->json(['data' => $savings_charge, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        SavingsCharge::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
