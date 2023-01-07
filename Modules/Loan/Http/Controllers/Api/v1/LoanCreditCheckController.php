<?php

namespace Modules\Loan\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laracasts\Flash\Flash;
use Modules\Loan\Entities\LoanCreditCheck;

class LoanCreditCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loan.loans.credit_checks.index'])->only(['index','show']);
        $this->middleware(['permission:loan.loans.credit_checks.create'])->only(['create','store']);
        $this->middleware(['permission:loan.loans.credit_checks.edit'])->only(['edit','update']);
        $this->middleware(['permission:loan.loans.credit_checks.destroy'])->only(['destroy']);

    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = LoanCreditCheck::all();
        return theme_view('loan::credit_check.index', compact('data'));
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_credit_check = LoanCreditCheck::find($id);
        return theme_view('loan::credit_check.show',compact('loan_credit_check'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_credit_check = LoanCreditCheck::find($id);
        return theme_view('loan::credit_check.edit',compact('loan_credit_check'));
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
            'translated_name' => ['required'],
            'security_level' => ['required'],
            'rating_type' => ['required'],
            'pass_min_amount' => ['required_if:rating_type,score'],
            'fail_max_amount' => ['required_if:rating_type,score'],
            'pass_max_amount' => ['required_if:rating_type,score'],
            'warn_min_amount' => ['required_if:rating_type,score'],
            'warn_max_amount' => ['required_if:rating_type,score'],
            'fail_min_amount' => ['required_if:rating_type,score'],
            'general_error_msg' => ['required'],
            'user_friendly_error_msg' => ['required'],
            'general_warning_msg' => ['required'],
            'user_friendly_warning_msg' => ['required'],
            'general_success_msg' => ['required'],
            'user_friendly_success_msg' => ['required'],
            'active' => ['required'],
        ]);
        $loan_credit_check = LoanCreditCheck::find($id);
        $loan_credit_check->translated_name = $request->translated_name;
        $loan_credit_check->security_level = $request->security_level;
        $loan_credit_check->rating_type = $request->rating_type;
        $loan_credit_check->pass_min_amount = $request->pass_min_amount;
        $loan_credit_check->pass_max_amount = $request->pass_max_amount;
        $loan_credit_check->warn_min_amount = $request->warn_min_amount;
        $loan_credit_check->warn_max_amount = $request->warn_max_amount;
        $loan_credit_check->fail_min_amount = $request->fail_min_amount;
        $loan_credit_check->fail_max_amount = $request->fail_max_amount;
        $loan_credit_check->user_friendly_error_msg = $request->user_friendly_error_msg;
        $loan_credit_check->general_warning_msg = $request->general_warning_msg;
        $loan_credit_check->user_friendly_warning_msg = $request->user_friendly_warning_msg;
        $loan_credit_check->general_success_msg = $request->general_success_msg;
        $loan_credit_check->user_friendly_success_msg = $request->user_friendly_success_msg;
        $loan_credit_check->active = $request->active;
        $loan_credit_check->save();
        activity()->on($loan_credit_check)
            ->withProperties(['id' => $loan_credit_check->id])
            ->log('Update Loan Credit Check');
        Flash::success(trans_choice("core::general.successfully_saved", 1));
        return redirect('loan/credit_check');
    }


}
