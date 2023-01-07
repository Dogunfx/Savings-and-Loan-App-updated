<?php

namespace Modules\Paypal\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Paypal\Paypal;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;

class PaypalController extends Controller
{
    public function get_loan_intent(Request $request)
    {
        $paypal_class = new Paypal($request->loan_id,'loan');
        if ($paypal_class->getSetting('paypal.test_mode') == 'yes') {
            $environment = new SandboxEnvironment($paypal_class->getSetting('paypal.client_id'), $paypal_class->getSetting('paypal.client_secret'));
        } else {
            $environment = new ProductionEnvironment($paypal_class->getSetting('paypal.client_id'), $paypal_class->getSetting('paypal.client_secret'));
        }
        $client = new PayPalHttpClient($environment);
        $order = new OrdersCreateRequest();
        $order->prefer('return=representation');
        $order->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => $request->loan_id,
                "amount" => [
                    "value" => $request->amount,
                    "currency_code" => $paypal_class->getSetting('paypal.currency_code')
                ]
            ]],
            "application_context" => [
                "cancel_url" => $paypal_class->getCancelUrl(),
                "return_url" => $paypal_class->getReturnUrl()
            ]
        ];
        try {
            $response = $client->execute($order)->result;
            return response()->json(['approval_link' => $response->links[1]->href]);
        } catch (HttpException $ex) {
            // This will print the detailed information on the exception.
            //REALLY HELPFUL FOR DEBUGGING
            return response()->json(['success' => false, 'message' => $ex->getMessage()], 422);
        }

    }

    public function capture_payment(Request $request)
    {
        if ($request->get('success') == true) {
            $paypal_class = new Paypal($request->loan_id,'loan');
            if ($paypal_class->getSetting('paypal.test_mode') == 'yes') {
                $environment = new SandboxEnvironment($paypal_class->getSetting('paypal.client_id'), $paypal_class->getSetting('paypal.client_secret'));
            } else {
                $environment = new ProductionEnvironment($paypal_class->getSetting('paypal.client_id'), $paypal_class->getSetting('paypal.client_secret'));
            }
            $client = new PayPalHttpClient($environment);
            $order = new OrdersCaptureRequest($request->token);
            $order->prefer('return=representation');
            try {
                $response = $client->execute($order)->result;
                if ($response->status == 'COMPLETED') {
                    $amount = 0;
                    foreach ($response->purchase_units as $purchase_unit) {
                        foreach ($purchase_unit->payments->captures as $capture) {
                            $amount += $capture->amount->value;
                        }
                    }
                    $paypal_class->processPayment([
                        'module' => 'loan',
                        'loan_id' => $request->loan_id,
                        'reference' => $response->id,
                        'amount' => $amount,
                    ]);
                    \flash('Thank you for making payment.')->success()->important();
                    return redirect('portal/loan/' . $request->loan_id . '/show');
                } else {
                    \flash('Payment was not completed.')->warning()->important();
                    return redirect('portal/loan/' . $request->loan_id . '/show');
                }

            } catch (HttpException $ex) {
                // This will print the detailed information on the exception.
                //REALLY HELPFUL FOR DEBUGGING
                //echo $ex->getData();
                Log::error($ex->getMessage());
                \flash('We could not process your payment, try again.')->warning()->important();
                return redirect('portal/loan/' . $request->loan_id . '/show');
            }

        } else {
            \flash('Payment was cancelled.')->warning()->important();
            return redirect('portal/loan/' . $request->loan_id . '/show');
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function webhook(Request $request)
    {
        $paypal_class = new Paypal();
        $payload = $request->getContent();
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypal_class->getSetting('client_id'),
                $paypal_class->getSetting('client_secret')
            )
        );
        /**
         * Receive HTTP headers that you received from PayPal webhook.
         * Just uncomment the below line to read the data from actual request.
         */
        /** @var  $headers */
        $headers = getallheaders();

        $headers = array_change_key_case($headers, CASE_UPPER);

        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
        $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
        $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
        $signatureVerification->setWebhookId("9XL90610J3647323C"); // Note that the Webhook ID must be a currently valid Webhook that you created with your client ID/secret.
        $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
        $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);

        $signatureVerification->setRequestBody($payload);
        $request_result = clone $signatureVerification;

        try {
            /** @var \PayPal\Api\VerifyWebhookSignatureResponse $output */
            $output = $signatureVerification->post($apiContext);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            exit(1);
        }
        if ($output->getVerificationStatus() == 'completed') {

        }
        http_response_code(200);
        exit();

    }
}
