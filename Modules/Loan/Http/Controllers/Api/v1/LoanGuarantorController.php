<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientRelationship;
use Modules\Client\Entities\ClientType;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;
use Modules\Loan\Entities\LoanGuarantor;

class LoanGuarantorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.guarantors.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.guarantors.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.guarantors.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.guarantors.destroy'])->only(['destroy']);

    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => ['required_if:is_client,0'],
            'last_name' => ['required_if:is_client,0'],
            'gender' => ['required_if:is_client,0'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => ['required_if:is_client,0', 'date'],
            'client_relationship_id' => ['required_if:is_client,0'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client = Client::find($request->client_id);
            $loan_guarantor = new LoanGuarantor();
            $loan_guarantor->created_by_id = Auth::id();
            $loan_guarantor->loan_id = $id;
            $loan_guarantor->client_id = $request->client_id;
            $loan_guarantor->is_client = $request->is_client;
            $loan_guarantor->client_relationship_id = $request->client_relationship_id;
            (!empty($client)) ? $loan_guarantor->first_name = $client->first_name : $loan_guarantor->first_name = $request->first_name;
            (!empty($client)) ? $loan_guarantor->last_name = $client->last_name : $loan_guarantor->last_name = $request->last_name;
            (!empty($client)) ? $loan_guarantor->gender = $client->gender : $loan_guarantor->gender = $request->gender;
            (!empty($client)) ? $loan_guarantor->country_id = $client->country_id : $loan_guarantor->country_id = $request->country_id;
            (!empty($client)) ? $loan_guarantor->title_id = $client->title_id : $loan_guarantor->title_id = $request->title_id;
            (!empty($client)) ? $loan_guarantor->profession_id = $client->profession_id : $loan_guarantor->profession_id = $request->profession_id;
            (!empty($client)) ? $loan_guarantor->mobile = $client->mobile : $loan_guarantor->mobile = $request->mobile;
            (!empty($client)) ? $loan_guarantor->notes = $client->notes : $loan_guarantor->notes = $request->notes;
            (!empty($client)) ? $loan_guarantor->email = $client->email : $loan_guarantor->email = $request->email;
            (!empty($client)) ? $loan_guarantor->address = $client->address : $loan_guarantor->address = $request->address;
            (!empty($client)) ? $loan_guarantor->marital_status = $client->marital_status : $loan_guarantor->marital_status = $request->marital_status;
            (!empty($client)) ? $loan_guarantor->dob = $client->dob : $loan_guarantor->dob = $request->dob;
            $loan_guarantor->guaranteed_amount = $request->guaranteed_amount;
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/loans');
                $loan_guarantor->photo = basename($file_name);
            }
            $loan_guarantor->save();
            return response()->json(['data' => $loan_guarantor, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_guarantor = LoanGuarantor::find($id);
        return response()->json(['data' => $loan_guarantor]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {

        $loan_guarantor = LoanGuarantor::find($id);
        return response()->json(['data' => $loan_guarantor]);
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
            'first_name' => ['required_if:is_client,0'],
            'last_name' => ['required_if:is_client,0'],
            'gender' => ['required_if:is_client,0'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => ['required_if:is_client,0', 'date'],
            'client_relationship_id' => ['required_if:is_client,0'],
            'photo' => ['image', 'mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client = Client::find($request->client_id);
            $loan_guarantor = LoanGuarantor::find($id);
            $loan_guarantor->client_id = $request->client_id;
            $loan_guarantor->client_relationship_id = $request->client_relationship_id;
            (!empty($client)) ? $loan_guarantor->first_name = $client->first_name : $loan_guarantor->first_name = $request->first_name;
            (!empty($client)) ? $loan_guarantor->last_name = $client->last_name : $loan_guarantor->last_name = $request->last_name;
            (!empty($client)) ? $loan_guarantor->gender = $client->gender : $loan_guarantor->gender = $request->gender;
            (!empty($client)) ? $loan_guarantor->country_id = $client->country_id : $loan_guarantor->country_id = $request->country_id;
            (!empty($client)) ? $loan_guarantor->title_id = $client->title_id : $loan_guarantor->title_id = $request->title_id;
            (!empty($client)) ? $loan_guarantor->profession_id = $client->profession_id : $loan_guarantor->profession_id = $request->profession_id;
            (!empty($client)) ? $loan_guarantor->mobile = $client->mobile : $loan_guarantor->mobile = $request->mobile;
            (!empty($client)) ? $loan_guarantor->notes = $client->notes : $loan_guarantor->notes = $request->notes;
            (!empty($client)) ? $loan_guarantor->email = $client->email : $loan_guarantor->email = $request->email;
            (!empty($client)) ? $loan_guarantor->address = $client->address : $loan_guarantor->address = $request->address;
            (!empty($client)) ? $loan_guarantor->marital_status = $client->marital_status : $loan_guarantor->marital_status = $request->marital_status;
            (!empty($client)) ? $loan_guarantor->dob = $client->dob : $loan_guarantor->dob = $request->dob;
            $loan_guarantor->guaranteed_amount = $request->guaranteed_amount;
            if ($request->hasFile('photo')) {
                $file_name = $request->file('photo')->store('public/uploads/loans');
                $loan_guarantor->photo = basename($file_name);
            }
            $loan_guarantor->save();
            return response()->json(['data' => $loan_guarantor, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_guarantor = LoanGuarantor::find($id);
        $loan_guarantor->delete();
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
