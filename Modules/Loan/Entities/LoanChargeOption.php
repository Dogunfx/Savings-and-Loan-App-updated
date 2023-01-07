<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanChargeOption extends Model
{
    protected $fillable = [];
    public $table = "loan_charge_options";
    public $timestamps = false;
}
