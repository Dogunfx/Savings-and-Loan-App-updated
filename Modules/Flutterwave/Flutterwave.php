<?php

namespace Modules\Flutterwave;

use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\Client;
use Modules\Core\PaymentGateways\PaymentGateway;

use Nwidart\Modules\Facades\Module;

class Flutterwave extends PaymentGateway
{
    public function __construct($resource_id = null, $module = null)
    {
        $this->resource_id = $resource_id;
        $this->module = $module;
        $this->setName('Flutterwave');
        $this->description = "Pay via Flutterwave";
        $this->setResultUrl(url('webhooks/flutterwave'));
        $this->setReturnUrl(url('flutterwave/capture_payment?resource_id=' . $this->resource_id . '&module=' . $this->module));
        $this->setCancelUrl(url('portal/loan/' . $this->resource_id . '/show?error=' . urlencode("Payment cancelled")));
        $this->setLogo(Module::asset('flutterwave:images/logo.png'));
        $this->setSettings();
        $this->processSettings();
        $this->setHtml($this->checkoutJs());
    }

    public function setSettings()
    {
        $this->settings = [
            [
                'name' => 'Status',
                'setting_key' => strtolower($this->name) . '.status',
                'setting_value' => $this->active,
                'category' => 'other',
                'type' => 'select',
                'options' => 'active,inactive',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
            [
                'name' => 'Name',
                'setting_key' => 'flutterwave.gateway_name',

                'setting_value' => 'Flutterwave',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ], [
                'name' => 'Logo',
                'setting_key' => 'flutterwave.logo',
                'setting_value' => 'logo.png',
                'category' => 'other',
                'type' => 'file',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],

            [
                'name' => 'Public Key',
                'setting_key' => 'flutterwave.public_key',

                'setting_value' => '',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
            [
                'name' => 'Secret Key',
                'setting_key' => 'flutterwave.secret_key',

                'setting_value' => '',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ], [
                'name' => 'Encryption Key',
                'setting_key' => 'flutterwave.encryption_key',

                'setting_value' => '',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ], [
                'name' => 'Webhook Secret Hash',
                'setting_key' => 'flutterwave.webhook_secret_hash',

                'setting_value' => '',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
            [
                'name' => 'Test Mode',
                'setting_key' => 'flutterwave.test_mode',

                'setting_value' => 'yes',
                'category' => 'other',
                'type' => 'select',
                'options' => 'yes,no',
                'class' => '',
                'required' => '0',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
            [
                'name' => 'Currency Code',
                'setting_key' => 'flutterwave.currency_code',

                'setting_value' => 'USD',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ], [
                'name' => 'Payment Options',
                'setting_key' => 'flutterwave.payment_options',

                'setting_value' => 'card, mobilemoneyghana, ussd',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
        ];
    }

    public function checkoutJs()
    {
        $public_key = $this->getSetting('flutterwave.public_key');
        $currency_code = $this->getSetting('flutterwave.currency_code');
        $payment_options = $this->getSetting('flutterwave.payment_options');
        $payment_successful_message = __('Payment Successful');
        $intent_url = url("flutterwave/get_loan_intent");
        $customer_email = '';
        $customer_phone = '';
        $customer_name = '';
        $customer_id = '';
        $client = Client::find(session('client_id'));
        if ($client) {
            $customer_email = $client->email;
            $customer_phone = $client->mobile;
            $customer_name = $client->name;
            $customer_id = $client->id;
        }
        $module = $this->module;
        $loan_id = $this->resource_id;
        $redirect_url = $this->return_url;
        $html = '';
        $html .= "";
        $html .= '';
        $html .= <<<EOT
<script>
$.getScript("https://checkout.flutterwave.com/v3.js", function(data, textStatus, jqxhr) {
    $('#gateway_info').append('<div id="card-element"></div><div id="card-errors" role="alert"></div>');
    $("#pay_button").removeAttr('disabled','disabled');
    var submitButton = document.getElementById('pay_button');
    submitButton.addEventListener('click', function(ev) {
      var amount= $('#payment_amount').val();
      $("#pay_button").attr('disabled','disabled');
      ev.preventDefault();
      FlutterwaveCheckout({
          public_key: "$public_key",
          tx_ref: "$loan_id",
          amount: amount,
          currency: "$currency_code",
          payment_options: "$payment_options",
          redirect_url: // specified redirect URL
            "$redirect_url",
          meta: {
            consumer_id: "$customer_id",
            module: "$module",
          },
          customer: {
            email: "$customer_email",
            phone_number: "$customer_phone",
            name: "$customer_name",
             module: "$module",
          },
          callback: function (data) {
            console.log(data);
          },
          onclose: function() {
            // close modal
          },
          customizations: {
            title: "Pay $this->module",
          
          },
    });
     })
});
</script>
EOT;

        return $html;
    }
}