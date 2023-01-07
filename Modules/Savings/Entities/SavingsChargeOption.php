<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;

class SavingsChargeOption extends Model
{
    protected $fillable = [];
    public $table = "savings_charge_options";
    public $timestamps = false;
}
