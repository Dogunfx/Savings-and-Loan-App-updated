<?php

namespace Modules\Mpesa;

use Modules\Core\PaymentGateways\PaymentGateway;

use Nwidart\Modules\Facades\Module;

class Mpesa extends PaymentGateway
{
    public function __construct($resource_id = null, $module = null)
    {
        $this->resource_id = $resource_id;
        $this->module = $module;
        $this->setName('Mpesa');
        $this->description = "Pay via Mpesa";
        $this->setResultUrl(url('webhooks/mpesa'));
        $this->setReturnUrl(url('mpesa/capture_payment?loan_id=' . $this->resource_id . '&success=true'));
        $this->setCancelUrl(url('portal/loan/' . $this->resource_id . '/show?error=' . urlencode("Payment cancelled")));
        $this->setLogo(Module::asset('mpesa:images/logo.png'));
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
                'setting_key' => 'mpesa.gateway_name',

                'setting_value' => 'Mpesa',
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
                'setting_key' => 'mpesa.logo',
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
                'name' => 'Consumer Key',
                'setting_key' => 'mpesa.consumer_key',

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
                'name' => 'Consumer Secret',
                'setting_key' => 'mpesa.consumer_secret',

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
                'name' => 'Passkey',
                'setting_key' => 'mpesa.passkey',

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
                'name' => 'Business Shortcode',
                'setting_key' => 'mpesa.business_shortcode',

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
                'name' => 'Sandbox URl',
                'setting_key' => 'mpesa.sandbox_url',

                'setting_value' => 'https://sandbox.safaricom.co.ke',
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
                'name' => 'Live URl',
                'setting_key' => 'mpesa.live_url',

                'setting_value' => 'https://api.safaricom.co.ke',
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
                'setting_key' => 'mpesa.test_mode',

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
                'setting_key' => 'mpesa.currency_code',

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
        $intent_url = url("mpesa/capture_payment");
        $module = $this->module;
        $loan_id = $this->resource_id;
        $redirect_url = url("portal/loan/" . $loan_id . '/show');
        $form_url = url("mpesa/capture_payment");
        $html = '';
        $html .= "";
        $html .= '';
        $html .= <<<EOT
        <script>
         $("#pay_button").removeAttr('disabled','disabled');
         $("#payment_form").attr('action',"$form_url");
         $('#gateway_info').append('<div class="form-group"> <input type="text" name="mpesa_mobile"  class="form-control" placeholder="Enter Mpesa Mobile Number"  id="mpesa_mobile" required/></div>');
         var submitButton = document.getElementById('pay_button');
         submitButton.addEventListener('click', function(ev) {
            var amount= $('#payment_amount').val();
            var mobile= $('#mpesa_mobile').val();
            $("#pay_button").attr('disabled','disabled');
            ev.preventDefault();
            axios.post('$intent_url', {
            amount: amount,
            module: "$module",
            loan_id: "$loan_id",
            mobile: mobile,
          }).then(function (response) {
              console.log(response.data);
          
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