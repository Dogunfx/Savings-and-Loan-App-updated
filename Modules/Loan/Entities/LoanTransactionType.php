<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanTransactionType extends Model
{
    protected $fillable = [];
    public $table = "loan_transaction_types";
    public $timestamps = false;
}
