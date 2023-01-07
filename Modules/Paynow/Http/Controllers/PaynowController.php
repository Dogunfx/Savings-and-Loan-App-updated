<?php

namespace Modules\Paynow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentGateway;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\TransactionUpdated;
use Modules\Paynow\Paynow;

class PaynowController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function webhook(Request $request)
    {
        $class = new Paynow();
        if ($request->module == 'loan') {
            $paynow = new \Paynow\Payments\Paynow(
                $class->getSetting('integration_id'),
                $class->getSetting('integration_key'),
                url('portal/loan/' . $request->id . '/show?module=loan&id=' . $request->id),
                $class->getResultUrl()
            );
            $status = $paynow->processStatusUpdate();
            if ($status->paid()) {
                $class->processPayment([
                    'module' => 'loan',
                    'loan_id' => $request->id,
                    'amount'=>$status->amount(),
                    'reference'=>$status->paynowReference()
                ]);

            }
        }
    }

}
