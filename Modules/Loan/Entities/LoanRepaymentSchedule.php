<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanRepaymentSchedule extends Model
{
    protected $fillable = ['principal_repaid_derived', 'fees_repaid_derived', 'interest_repaid_derived', 'penalties_repaid_derived'];
    public $table = "loan_repayment_schedules";

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }
}
