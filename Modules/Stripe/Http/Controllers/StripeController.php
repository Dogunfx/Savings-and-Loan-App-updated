<?php

namespace Modules\Stripe\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\TransactionUpdated;
use Modules\Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function get_loan_intent(Request $request)
    {
        $stripe_class = new Stripe();
        \Stripe\Stripe::setApiKey($stripe_class->getSetting('stripe.secret_key'));
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $request->amount * 100,
            'currency' => $stripe_class->getSetting('stripe.currency_code'),
            'metadata' => [
                'module' => 'loan',
                'loan_id' => $request->loan_id
            ]
        ]);
        return response()->json(['intent' => $intent->client_secret]);
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
                    'amount'=>$intent->amount/100,
                    'reference'=>$intent->id
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
