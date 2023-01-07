<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Modules\CustomField\Entities\CustomField;
use Modules\Loan\Entities\LoanCollateral;
use Modules\Loan\Entities\LoanCollateralType;

class LoanCollateralController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.collateral.index'])->only(['index','show']);
        $this->middleware(['permission:loan.loans.collateral.create'])->only(['create','store']);
        $this->middleware(['permission:loan.loans.collateral.edit'])->only(['edit','update']);
        $this->middleware(['permission:loan.loans.collateral.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('loan::loan_collateral.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        $loan_collateral_types = LoanCollateralType::get();
        $custom_fields = CustomField::where('category', 'add_collateral')->where('active', 1)->get();
        return theme_view('loan::loan_collateral.create', compact('loan_collateral_types','id','custom_fields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'loan_collateral_type_id' => ['required'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
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
        activity()->on($loan_collateral)
            ->withProperties(['id' => $loan_collateral->id])
            ->log('Create Loan Collateral');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_collateral = LoanCollateral::find($id);
        $custom_fields = CustomField::where('category', 'add_collateral')->where('active', 1)->get();
        return theme_view('loan::loan_collateral.show', compact('loan_collateral','custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_collateral_types = LoanCollateralType::get();
        $loan_collateral = LoanCollateral::find($id);
        $custom_fields = CustomField::where('category', 'add_collateral')->where('active', 1)->get();
        return theme_view('loan::loan_collateral.edit', compact('loan_collateral_types', 'loan_collateral','custom_fields'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'loan_collateral_type_id' => ['required'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        $loan_collateral = LoanCollateral::find($id);
        $loan_collateral->loan_collateral_type_id = $request->loan_collateral_type_id;
        $loan_collateral->value = $request->value;
        $loan_collateral->description = $request->description;
        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->store('public/uploads/loans');
            $loan_collateral->link = basename($file_name);
        }
        $loan_collateral->save();
        activity()->on($loan_collateral)
            ->withProperties(['id' => $loan_collateral->id])
            ->log('Update Loan Collateral');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan_collateral->loan_id . '/show');
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
        activity()->on($loan_collateral)
            ->withProperties(['id' => $loan_collateral->id])
            ->log('Delete Loan Collateral');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
