<?php

namespace Modules\Paynow;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\Core\PaymentGateways\PaymentGateway;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Setting\Entities\Setting;
use Nwidart\Modules\Facades\Module;

class Paynow extends PaymentGateway
{
    public function __construct()
    {
        $this->setName('Paynow');
        $this->setResultUrl(url('webhooks/paynow/webhook'));
        $this->setLogo(Module::asset('paynow:images/logo.png'));
        $this->setSettings();
        $this->processSettings();
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
                'setting_key' => 'paynow.gateway_name',
                'module' => 'Paynow',
                'setting_value' => 'Paynow',
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
                'name' => 'Integration ID',
                'setting_key' => 'paynow.integration_id',
                'module' => 'Paynow',
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
                'name' => 'Integration Key',
                'setting_key' => 'paynow.integration_key',
                'module' => 'Paynow',
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
                'name' => 'Exchange Rate',
                'setting_key' => 'paynow.exchange_rate',
                'module' => 'Paynow',
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

    public function processPaymentOld($data)
    {
        $loan = Loan::with('loan_product')->find($data['id']);
        $paynow = new \Paynow\Payments\Paynow(
            $this->getSetting('integration_id'),
            $this->getSetting('integration_key'),
            $data['return_url'].'?module=loan&id='.$loan->id,
            $this->result_url
        );
        if ($data['module'] == 'loan') {
            $payment_type = PaymentType::find($this->getSetting('payment_type_id'));
            $payment_gateway = \Modules\Core\Entities\PaymentGateway::where('name', 'Paynow')->first();
            //payment details
            $payment_detail = new PaymentDetail();
            $payment_detail->created_by_id = Auth::id();
            $payment_detail->payment_type_id = $payment_type->id;
            $payment_detail->transaction_type = 'loan_transaction';
            $payment_detail->save();
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Auth::id();
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->name = trans_choice('loan::general.repayment', 1);
            $loan_transaction->loan_transaction_type_id = 2;
            $loan_transaction->online_transaction = 1;
            $loan_transaction->gateway_id = $payment_gateway->id;
            $loan_transaction->status = 'pending';
            $loan_transaction->submitted_on = date("Y-m-d");
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $data['amount'];
            $loan_transaction->credit = $data['amount'];
            $loan_transaction->save();
            $payment = $paynow->createPayment($loan_transaction->id, Auth::user()->email);
            $payment->add(trans_choice('loan::general.loan', 1) . ' #' . $loan->id, $data['amount']);
            $response = $paynow->send($payment);
            if ($response->success()) {
                $link = $response->redirectUrl();
                $pollUrl = $response->pollUrl();
                $loan_transaction->payment_gateway_data = $pollUrl;
                $loan_transaction->save();
                ob_clean();
                header('Location: ' . $link);
                exit();
            } else {
                $payment_detail->delete();
                $loan_transaction->delete();
            }
        }
    }
}