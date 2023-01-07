<?php

namespace Modules\Stripe;

use Modules\Core\PaymentGateways\PaymentGateway;

use Nwidart\Modules\Facades\Module;

class Stripe extends PaymentGateway
{
    public function __construct($resource_id = null, $module = null)
    {
        $this->resource_id = $resource_id;
        $this->module = $module;
        $this->setName('Stripe');
        $this->setResultUrl(url('stripe/stripe_result'));
        $this->setLogo(Module::asset('stripe:images/logo.png'));
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
                'setting_key' => 'stripe.gateway_name',
                'module' => 'Stripe',
                'setting_value' => 'Stripe',
                'category' => 'other',
                'type' => 'text',
                'options' => '',
                'class' => '',
                'required' => '1',
                'db_columns' => '',
                'displayed' => '0',
                'rules' => ''
            ],
            [
                'name' => 'Publishable key',
                'setting_key' => 'stripe.publishable_key',
                'module' => 'Stripe',
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
                'name' => 'Secret key',
                'setting_key' => 'stripe.secret_key',
                'module' => 'Stripe',
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
                'name' => 'Webhook Signing secret',
                'setting_key' => 'stripe.webhook_signing_secret',
                'module' => 'Stripe',
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
                'name' => 'Currency Code',
                'setting_key' => 'stripe.currency_code',
                'module' => 'Stripe',
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
        ];
    }

    public function checkoutJs()
    {
        $publishable_key = $this->getSetting('stripe.publishable_key');
        $payment_successful_message = __('Payment Successful');
        $intent_url = url("stripe/get_loan_intent");
        $module = 'loan';
        $loan_id = $this->resource_id;
        $redirect_url = url("portal/loan/" . $loan_id . '/show');
        $html = '';
        $html .= "";
        $html .= '';
        $html .= <<<EOT
<script>
$.getScript("https://js.stripe.com/v3/", function(data, textStatus, jqxhr) {
    $('#gateway_info').append('<div id="card-element"></div><div id="card-errors" role="alert"></div>');
    $("#pay_button").removeAttr('disabled','disabled');
    var stripe = Stripe('$publishable_key');
    var clientSecret='';
    var elements = stripe.elements();
    var style = {
      base: {
        color: "#32325d",
      }
     };
    var card = elements.create("card", { style: style });
    card.mount("#card-element");
    card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });
    var submitButton = document.getElementById('pay_button');
    submitButton.addEventListener('click', function(ev) {
       var amount= $('#payment_amount').val();
       $("#pay_button").attr('disabled','disabled');
       ev.preventDefault();
      axios.post('$intent_url', {
            amount: amount,
            module: "$module",
            loan_id: "$loan_id",
          }).then(function (response) {
       clientSecret=response.data.intent;
       stripe.confirmCardPayment(clientSecret, {
        payment_method: {
          card: card,

        }
   }).then(function(result) {
    if (result.error) {
      toastr.warning(result.error.message);
       $("#pay_button").removeAttr('disabled');
    } else {
      // The payment has been processed!
      if (result.paymentIntent.status === 'succeeded') {
          toastr.success("$payment_successful_message");
          window.setTimeout(function(){
            window.location.href = "$redirect_url";
          }, 2000);
      }
    }
    });
  })
  .catch(function (error) {

  });

  });

});
</script>
EOT;

        return $html;
    }
}