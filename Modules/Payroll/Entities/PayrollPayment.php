<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\PaymentDetail;
use Modules\User\Entities\User;

class PayrollPayment extends Model
{
    protected $fillable = [];
    public $table = "payroll_payments";

    public function payroll_items()
    {
        return $this->hasMany(PayrollItemMeta::class, 'payroll_id', 'id');
    }
    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }
    public function payroll()
    {
        return $this->hasOne(Payroll::class, 'id', 'payroll_id');
    }
}
