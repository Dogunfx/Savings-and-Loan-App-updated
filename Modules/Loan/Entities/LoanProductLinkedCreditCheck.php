<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanProductLinkedCreditCheck extends Model
{
    protected $fillable = [];
    public $table = "loan_product_linked_credit_checks";
}
