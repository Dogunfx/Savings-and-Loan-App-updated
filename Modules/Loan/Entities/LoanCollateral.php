<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCollateral extends Model
{
    protected $fillable = [];
    public $table = "loan_collateral";

    public function collateral_type()
    {
        return $this->hasOne(LoanCollateralType::class, 'id', 'loan_collateral_type_id');
    }
}
