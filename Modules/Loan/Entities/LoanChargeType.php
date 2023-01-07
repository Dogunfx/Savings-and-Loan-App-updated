<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanChargeType extends Model
{
    protected $fillable = [];
    public $table = "loan_charge_types";
    public $timestamps = false;
}
