<?php

namespace Modules\Wallet\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\Currency;
use Modules\User\Entities\User;

class Wallet extends Model
{
    protected $fillable = [];
    protected $table = 'wallets';

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function submitted_by()
    {
        return $this->hasOne(User::class, 'id', 'submitted_by_user_id');
    }
    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_user_id');
    }
    public function activated_by()
    {
        return $this->hasOne(User::class, 'id', 'activated_by_user_id');
    }
}
