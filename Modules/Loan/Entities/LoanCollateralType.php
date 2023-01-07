<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCollateralType extends Model
{
    protected $fillable = [];
    public $table = "loan_collateral_types";
    public $timestamps = false;
}
