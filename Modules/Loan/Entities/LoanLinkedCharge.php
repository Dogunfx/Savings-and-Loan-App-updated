<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanLinkedCharge extends Model
{
    protected $fillable = [];
    public $table = "loan_linked_charges";

    public function charge()
    {
        return $this->hasOne(LoanCharge::class, 'id', 'loan_charge_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function transaction()
    {
        return $this->hasOne(LoanTransaction::class, 'id', 'loan_transaction_id');
    }
}
