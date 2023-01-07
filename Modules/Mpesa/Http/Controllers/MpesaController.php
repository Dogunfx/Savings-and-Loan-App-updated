<?php

namespace Modules\Mpesa\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Mpesa\Mpesa;

class MpesaController extends Controller
{


    public function capture_payment(Request $request)
    {
        $mpesa_class = new Mpesa($request->loan_id, 'loan');
        if ($mpesa_class->getSetting('mpesa.test_mode') == 'yes') {
            $url = $mpesa_class->getSetting('mpesa.sandbox_url');
        } else {
            $url = $mpesa_class->getSetting('mpesa.live_url');
        }
        $consumer_secret = $mpesa_class->getSetting('mpesa.consumer_secret');
        $consumer_key = $mpesa_class->getSetting('mpesa.consumer_key');
        $business_shortcode = $mpesa_class->getSetting('mpesa.business_shortcode');
        $passkey = $mpesa_class->getSetting('mpesa.passkey');
        $timestamp=Carbon::now()->format('YmdHms');
        $access_token = Http::withHeaders([
            'Authorization: Basic ' => base64_encode($consumer_key . ":" . $consumer_secret)
        ])
            ->get($url . '/oauth/v1/generate?grant_type=client_credentials')['access_token'];
        $response = Http::withToken($access_token)
            ->post($url . '/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $mpesa_class->getSetting('mpesa.business_shortcode'),
                'Password' => base64_encode($business_shortcode.$passkey.$timestamp),
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount"' => $request->amount,
                'PartyA' => $request->mobile,
                'PartyB' => $mpesa_class->getSetting('mpesa.business_shortcode'),
                'PhoneNumber' => $request->mobile,
                'CallBackURL' => $mpesa_class->getResultUrl(),
                'AccountReference' => $request->loan_id,
                'TransactionDesc' => 'Loan Repayment'
            ]);
        return $response;
    }

    public function webhook(Request $request)
    {
        $content=json_decode($request->getContent());
        $mpesa_class = new Mpesa($content->InvoiceNumber, 'loan');
        $mpesa_class->processPayment([
            'module' => 'loan',
            'loan_id' => $content->InvoiceNumber,
            'reference' => $content->TransID,
            'amount' => $content->TransAmount,
        ]);
        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));
        return $response;
    }
}
