<?php

namespace Modules\Wallet\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\PaymentDetail;
use Modules\User\Entities\User;

class WalletTransaction extends Model
{
    protected $fillable = [];
    protected $table = 'wallet_transactions';

    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'id', 'wallet_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
