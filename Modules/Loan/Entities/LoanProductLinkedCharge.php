<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanProductLinkedCharge extends Model
{
    protected $fillable = [];
    public $table = "loan_product_linked_charges";

    public function charge()
    {
        return $this->hasOne(LoanCharge::class, 'id', 'loan_charge_id');
    }
}
