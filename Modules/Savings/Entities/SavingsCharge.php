<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;

class SavingsCharge extends Model
{
    protected $fillable = [];
    public $table = "savings_charges";

    public function charge_type()
    {
        return $this->hasOne(SavingsChargeType::class, 'id', 'savings_charge_type_id');
    }

    public function charge_option()
    {
        return $this->hasOne(SavingsChargeOption::class, 'id', 'savings_charge_option_id');
    }
}
