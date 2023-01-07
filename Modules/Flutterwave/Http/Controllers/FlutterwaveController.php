<?php

namespace Modules\Flutterwave\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Flutterwave\Flutterwave;
use function GuzzleHttp\json_decode;

class FlutterwaveController extends Controller
{
    public function capture_payment(Request $request)
    {

        $data = json_decode($request->resp);
        $flutterwave_class = new Flutterwave($data->tx->txRef, 'loan');
        if ($data->tx->status == 'successful') {
            $response = Http::withToken($flutterwave_class->getSetting('flutterwave.secret_key'))->get("https://api.flutterwave.com/v3/transactions/{$data->tx->id}/verify");
           $response_data=$response->json();
            $flutterwave_class->processPayment([
                'module' => 'loan',
                'loan_id' =>$response_data['data']['tx_ref'],
                'reference' => $response_data['data']['id'],
                'amount' => $response_data['data']['amount'],
            ]);
            \flash('Thank you for making payment.')->success()->important();
            return redirect('portal/loan/' . $response_data['data']['tx_ref'] . '/show');
        }
        \flash('Payment was cancelled.')->warning()->important();
        return redirect('portal/loan/' . $data->tx->txRef . '/show');

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('STRIPE_SIGNATURE');
        $class = new Stripe();
        $endpoint_secret = $class->getSetting('stripe.webhook_signing_secret');
        $cli_secret = 'whsec_aQpkqKhwAryZrGmM9Q814pNgfdQOnOJE';
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $cli_secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            Log::info($e);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            Log::info($e);
            exit();
        }

        if ($event->type == "payment_intent.succeeded") {
            $intent = $event->data->object;
            $charges = $intent->charges->data[0];
            if ($charges->metadata->module == 'loan') {
                $class->processPayment([
                    'module' => 'loan',
                    'loan_id' => $charges->metadata->loan_id,
                    'amount' => $intent->amount / 100,
                    'reference' => $intent->id
                ]);
            }
            printf("Succeeded: %s", $intent->id);
            http_response_code(200);
            exit();
        } elseif ($event->type == "payment_intent.payment_failed") {
            $intent = $event->data->object;
            $error_message = $intent->last_payment_error ? $intent->last_payment_error->message : "";
            printf("Failed: %s, %s", $intent->id, $error_message);
            http_response_code(200);
            exit();
        }

    }
}
