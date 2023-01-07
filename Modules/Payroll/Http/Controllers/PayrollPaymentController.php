<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\Payroll\Entities\Payroll;
use Modules\Payroll\Entities\PayrollPayment;

class PayrollPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:payroll.payroll.index'])->only(['index', 'show']);
        $this->middleware(['permission:payroll.payroll.create'])->only(['create', 'store']);
        $this->middleware(['permission:payroll.payroll.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:payroll.payroll.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('payroll::payroll_payment.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        $payment_types = PaymentType::where('active', 1)->get();
        $payroll = Payroll::find($id);
        $payments = PayrollPayment::where('payroll_id', $id)->sum('amount');
        $balance = $payroll->gross_amount - $payments;
        return theme_view('payroll::payroll_payment.create', compact('payment_types', 'id', 'balance'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $payroll = Payroll::find($id);
        $payments = PayrollPayment::where('payroll_id', $id)->sum('amount');
        $balance = $payroll->gross_amount - $payments;
        $request->validate([
            'amount' => ['required', 'numeric', 'max:' . $balance],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);

        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'payroll_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $payroll_payment = new PayrollPayment();
        $payroll_payment->created_by_id = Auth::id();
        $payroll_payment->payroll_id = $payroll->id;
        $payroll_payment->branch_id = $payroll->branch_id;
        $payroll_payment->payment_detail_id = $payment_detail->id;
        $payroll_payment->submitted_on = $request->date;
        $payroll_payment->amount = $request->amount;
        $payroll_payment->save();
        activity()->on($payroll_payment)
            ->withProperties(['id' => $payroll_payment->id])
            ->log('Create Payroll Payment');
        //fire transaction updated event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payroll/' . $payroll->id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $payroll = Payroll::find($id);
        return theme_view('payroll::payroll_payment.show', compact('payroll'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $payroll_payment = PayrollPayment::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        return theme_view('payroll::payroll_payment.edit', compact('payment_types', 'payroll_payment'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $payroll_payment = PayrollPayment::find($id);
        $payroll = $payroll_payment->payroll;
        $payments = PayrollPayment::where('payroll_id', $id)->sum('amount');
        $balance = $payroll->gross_amount - $payments + $payroll_payment->amount;
        $request->validate([
            'amount' => ['required', 'numeric', 'max:' . $balance],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);

        //payment details
        $payment_detail = PaymentDetail::find($payroll_payment->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'payroll_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $payroll_payment->submitted_on = $request->date;
        $payroll_payment->amount = $request->amount;
        $payroll_payment->save();
        activity()->on($payroll_payment)
            ->withProperties(['id' => $payroll_payment->id])
            ->log('Update Payroll Payment');
        //fire transaction updated event
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('payroll/' . $payroll->id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $payroll_payment= PayrollPayment::find($id);
        $payroll_payment->delete();
        PaymentDetail::destroy($payroll_payment->payment_detail_id);
        activity()->on($payroll_payment)
            ->withProperties(['id' => $payroll_payment->id])
            ->log('Delete Payroll Payment');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
