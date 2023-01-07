<?php

namespace Modules\Share\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\Currency;
use Modules\Savings\Entities\Savings;
use Modules\User\Entities\User;

class Share extends Model
{
    protected $fillable = [];

    public function charges()
    {
        return $this->hasMany(ShareLinkedCharge::class, 'share_id', 'id');
    }

    public function share_product()
    {
        return $this->belongsTo(ShareProduct::class, 'share_product_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function savings()
    {
        return $this->hasOne(Savings::class, 'id', 'savings_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function share_officer()
    {
        return $this->hasOne(User::class, 'id', 'share_officer_id');
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

    public function rejected_by()
    {
        return $this->hasOne(User::class, 'id', 'rejected_by_user_id');
    }

    public function written_off_by()
    {
        return $this->hasOne(User::class, 'id', 'written_off_by_user_id');
    }

    public function closed_by()
    {
        return $this->hasOne(User::class, 'id', 'closed_by_user_id');
    }

    public function withdrawn_by()
    {
        return $this->hasOne(User::class, 'id', 'withdrawn_by_user_id');
    }

    public function dormant_by()
    {
        return $this->hasOne(User::class, 'id', 'dormant_by_user_id');
    }

    public function inactive_by()
    {
        return $this->hasOne(User::class, 'id', 'inactive_by_user_id');
    }

    public function transactions()
    {
        return $this->hasMany(ShareTransaction::class, 'share_id', 'id')->orderBy('submitted_on', 'asc')->orderBy('id', 'asc');
    }
}
