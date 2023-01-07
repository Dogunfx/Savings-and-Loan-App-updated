<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanLinkedCreditCheck extends Model
{
    protected $fillable = [];
    public $table = "loan_linked_credit_checks";
}
