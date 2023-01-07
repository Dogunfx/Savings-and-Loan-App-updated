<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanPurpose extends Model
{
    protected $fillable = [];
    public $table = "loan_purposes";
    public $timestamps = false;
}
