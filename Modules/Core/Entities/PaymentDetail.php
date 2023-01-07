<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $table = "payment_details";
    protected $fillable = [];

    public function payment_type()
    {
        return $this->hasOne(PaymentType::class, 'id', 'payment_type_id');
    }
}
