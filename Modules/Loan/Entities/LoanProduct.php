<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $fillable = [];
    public $table = "loan_products";

    public function charges()
    {
        return $this->hasMany(LoanProductLinkedCharge::class, 'loan_product_id', 'id');
    }
}
