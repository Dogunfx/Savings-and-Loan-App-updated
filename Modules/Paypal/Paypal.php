<?php

namespace Modules\Paypal;

use Modules\Core\PaymentGateways\PaymentGateway;

use Nwidart\Modules\Facades\Module;

class Paypal extends PaymentGateway
{
    public function __construct($resource_id = null, $module = null)
    {
        $this->resource_id = $resource_id;
        $this->module = $module;
        $this->setName('Paypal');
        $this->description = "Pay via Paypal";
        $this->setResultUrl(url('webhooks/paypal'));
        $this->setReturnUrl(url('paypal/capture_payment?loan_id=' . $this->resource_id . '&success=true'));
        $this->setCancelUrl(url('portal/loan/' . $this->resource_id . '/show?error=' . urlencode("Payment cancelled")));
        $this->setLogo(Module::asset('paypal:images/logo.png'));
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
                'setting_key' => 'paypal.gateway_name',

                'setting_value' => 'Paypal',
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
                'setting_key' => 'paypal.logo',
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
                'name' => 'Client ID',
                'setting_key' => 'paypal.client_id',

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
                'name' => 'Client Secret',
                'setting_key' => 'paypal.client_secret',

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
                'name' => 'Webhook ID',
                'setting_key' => 'paypal.webhook_id',

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
                'setting_key' => 'paypal.test_mode',

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
                'setting_key' => 'paypal.currency_code',

                'setting_value' => 'USD',
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
        $payment_successful_message = __('Payment Successful');
        $intent_url = url("paypal/get_loan_intent");
        $module = $this->module;
        $loan_id = $this->resource_id;
        $redirect_url = url("portal/loan/" . $loan_id . '/show');
        $html = '';
        $html .= "";
        $html .= '';
        $html .= <<<EOT
        <script>
         $("#pay_button").removeAttr('disabled','disabled');
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
           window.setTimeout(function(){
                    window.location.href = response.data.approval_link;
                  }, 1000);
          }).catch(function (error) {
              toastr.warning(error.data.message);
               $("#pay_button").removeAttr('disabled');
          });
        });
        </script>
EOT;
        return $html;
    }
}