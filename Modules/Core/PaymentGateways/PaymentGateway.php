<?php


namespace Modules\Core\PaymentGateways;


use Illuminate\Http\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\TransactionUpdated;
use Modules\Setting\Entities\Setting;

class PaymentGateway
{
    protected $connection;
    protected $settings = [];
    protected $return_url;
    protected $result_url;
    protected $cancel_url;
    protected $name;
    protected $id;
    protected $resource_id;
    protected $module;
    protected $logo;
    protected $active = 'inactive';
    protected $html;
    protected $version;
    protected $description;

    public function getSettings()
    {
        foreach ($this->settings as $key => $value) {
            if ($setting = Setting::where('setting_key', $value['setting_key'])->first()) {
                $this->settings[$key]['setting_value'] = $setting->setting_value;
                $this->settings[$key]['id'] = $setting->id;
            } else {
                $this->settings[$key]['id'] = Str::random(16);
            }
        }
        return $this->settings;
    }

    public function uninstall()
    {
        PaymentType::where('name', $this->name)->delete();
        Setting::where('setting_key', 'like', '%' . $this->name . '.%')->delete();
        Artisan::call('module:migrate-rollback ' . $this->name);
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSetting($name)
    {
        $setting = '';
        foreach ($this->settings as $key) {
            if ($key['setting_key'] == $name) {
                $setting = $key['setting_value'];
                break;
            }
        }

        return $setting;
    }

    public function processSettings()
    {

        foreach ($this->settings as $key => $value) {
            if ($setting = Setting::where('setting_key', $value['setting_key'])->first()) {
                $this->settings[$key]['setting_value'] = $setting->setting_value;
                $this->settings[$key]['id'] = $setting->id;
            } else {
                $this->settings[$key]['id'] = Str::random();
            }
        }
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html): void
    {
        $this->html = $html;
    }

    /**
     * @param float $version
     */
    public function setVersion(float $version): void
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->return_url;
    }

    /**
     * @param mixed $return_url
     */
    public function setReturnUrl($return_url): void
    {
        $this->return_url = $return_url;
    }

    /**
     * @return mixed
     */
    public function getResultUrl()
    {
        return $this->result_url;
    }

    /**
     * @param mixed $result_url
     */
    public function setResultUrl($result_url): void
    {
        $this->result_url = $result_url;
    }

    /**
     * @return mixed
     */
    public function getCancelUrl()
    {
        return $this->cancel_url;
    }

    /**
     * @param mixed $cancel_url
     */
    public function setCancelUrl($cancel_url): void
    {
        $this->cancel_url = $cancel_url;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * @param null $resource_id
     */
    public function setResourceId($resource_id): void
    {
        $this->resource_id = $resource_id;
    }

    /**
     * @return null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param null $module
     */
    public function setModule($module): void
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getActive(): string
    {
        return $this->active;
    }

    /**
     * @param string $active
     */
    public function setActive(string $active): void
    {
        $this->active = $active;
    }


    public function install()
    {
        if (PaymentType::where('name', $this->name)->first()) {
            \flash(__('Gateway Already Installed'))->success()->important();
            Artisan::call('module:migrate ' . $this->name);
        } else {
            //make migrations
            Artisan::call('module:migrate ' . $this->name);
            //seed the db
            Artisan::call('module:seed ' . $this->name);
            //publish resources
            Artisan::call('module:publish ' . $this->name);
            //insert the gateway in payment gateways
            $payment_type = new PaymentType();
            $payment_type->name = $this->name;
            $payment_type->is_online = 1;
            $payment_type->description = $this->description;
            $payment_type->save();
            $this->setId($payment_type->id);
            foreach ($this->settings as $key => $value) {
                if (!$setting = Setting::where('setting_key', $value['setting_key'])->first()) {
                    unset($value['id']);
                    if ($value['name'] == 'Logo') {
                        if (file_exists($this->getLogo())) {
                            $file = Storage::putFile('public/uploads', new File($this->getLogo()));
                            $value['setting_value'] = basename($file);
                        }
                    }
                    DB::table('settings')->insert($value);
                }
            }
            \flash(__('Gateway Installed Successfully'))->success()->important();
        }
    }

    public function processPayment($data)
    {
        if ($data['module'] == 'loan') {
            $loan = Loan::with('loan_product')->find($data['loan_id']);
            $payment_type = PaymentType::where('name', $this->name)->first();
            //payment details
            $payment_detail = new PaymentDetail();
            $payment_detail->created_by_id = Auth::id();
            $payment_detail->payment_type_id = $payment_type->id;
            $payment_detail->transaction_type = 'loan_transaction';
            $payment_detail->receipt = $data['reference'];
            $payment_detail->description = "Payment via " . $this->name;
            $payment_detail->save();
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Auth::id();
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->name = trans_choice('loan::general.repayment', 1);
            $loan_transaction->loan_transaction_type_id = 2;
            $loan_transaction->online_transaction = 1;
            $loan_transaction->gateway_id = $payment_type->id;
            $loan_transaction->status = 'approved';
            $loan_transaction->submitted_on = date("Y-m-d");
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->reference = $data['reference'];
            $loan_transaction->amount = $data['amount'];
            $loan_transaction->credit = $data['amount'];
            $loan_transaction->save();
            //fire transaction updated event
            event(new TransactionUpdated($loan_transaction->loan));
        }
    }
}
