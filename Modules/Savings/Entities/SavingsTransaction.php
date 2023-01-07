<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\PaymentDetail;
use Modules\User\Entities\User;

class SavingsTransaction extends Model
{
    protected $fillable = [];
    public $table = "savings_transactions";

    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }

    public function savings()
    {
        return $this->hasOne(Savings::class, 'id', 'savings_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function savings_linked_charge()
    {
        return $this->hasOne(SavingsLinkedCharge::class, 'id', 'savings_linked_charge_id');
    }
}
