<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Loan\Entities\LoanNote;

class LoanNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.notes.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.notes.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.notes.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.notes.destroy'])->only(['destroy']);

    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'description' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_note = new LoanNote();
            $loan_note->created_by_id = Auth::id();
            $loan_note->loan_id = $id;
            $loan_note->description = $request->description;
            $loan_note->save();
            return response()->json(['data' => $loan_note, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_note = LoanNote::find($id);
        return response()->json(['data' => $loan_note]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_note = LoanNote::find($id);
        return response()->json(['data' => $loan_note]);
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
            'description' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_note = LoanNote::find($id);
            $loan_note->description = $request->description;
            $loan_note->save();
            return response()->json(['data' => $loan_note, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_note = LoanNote::find($id);
        $loan_note->delete();
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
