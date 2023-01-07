<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCharge extends Model
{
    protected $fillable = [];
    public $table = "loan_charges";

    public function charge_type()
    {
        return $this->hasOne(LoanChargeType::class, 'id', 'loan_charge_type_id');
    }
    public function charge_option()
    {
        return $this->hasOne(LoanChargeOption::class, 'id', 'loan_charge_option_id');
    }
}
