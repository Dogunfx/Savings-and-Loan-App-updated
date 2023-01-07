<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;

class SavingsChargeType extends Model
{
    protected $fillable = [];
    public $table="savings_charge_types";
    public $timestamps=false;
}
