<?php

namespace Modules\Share\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\PaymentDetail;

class ShareTransaction extends Model
{
    protected $fillable = [];

    public function share()
    {
        return $this->hasOne(Share::class, 'id', 'share_id');
    }

    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }
}
