<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class Payroll extends Model
{
    protected $fillable = [];
    public $table = "payroll";

    public function payroll_items()
    {
        return $this->hasMany(PayrollItemMeta::class, 'payroll_id', 'id');
    }
    public function payroll_payments()
    {
        return $this->hasMany(PayrollPayment::class, 'payroll_id', 'id');
    }
    public function payroll_template()
    {
        return $this->hasOne(PayrollTemplate::class, 'id', 'payroll_template_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }
}
