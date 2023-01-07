<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\PaymentDetail;
use Modules\User\Entities\User;

class LoanTransaction extends Model
{
    protected $fillable = [];
    public $table = "loan_transactions";

    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
