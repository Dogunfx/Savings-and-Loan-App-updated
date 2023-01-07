<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanHistory extends Model
{
    protected $fillable = [];
    public $table = "loan_history";
}
