<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCreditCheck extends Model
{
    protected $fillable = [];
    public $table = "loan_credit_checks";
}
