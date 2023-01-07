<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Loan\Entities\LoanFile;

class LoanFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:loan.loans.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.files.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.files.destroy'])->only(['destroy']);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_file = new LoanFile();
            $loan_file->created_by_id = Auth::id();
            $loan_file->loan_id = $id;
            $loan_file->name = $request->name;
            $loan_file->description = $request->description;
            if ($request->hasFile('file')) {
                $file_name = $request->file('file')->store('public/uploads/loans');
                $loan_file->link = basename($file_name);
            }
            $loan_file->save();
            return response()->json(['data' => $loan_file, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_file = LoanFile::find($id);
        return response()->json(['data' => $loan_file]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_file = LoanFile::find($id);
        return response()->json(['data' => $loan_file]);
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
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $loan_file = LoanFile::find($id);
            $loan_file->name = $request->name;
            $loan_file->description = $request->description;
            if ($request->hasFile('file')) {
                $file_name = $request->file('file')->store('public/uploads/loans');
                if ($loan_file->link) {
                    Storage::delete('public/uploads/loans/' . $loan_file->link);
                }
                $loan_file->link = basename($file_name);
            }
            $loan_file->save();
            return response()->json(['data' => $loan_file, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_file = LoanFile::find($id);
        if ($loan_file->link) {
            Storage::delete('public/uploads/loans/' . $loan_file->link);
        }
        $loan_file->delete();
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
