<?php

namespace Modules\Share\Entities;

use Illuminate\Database\Eloquent\Model;

class ShareCharge extends Model
{
    protected $fillable = [];

    public function charge_type()
    {
        return $this->hasOne(ShareChargeType::class, 'id', 'share_charge_type_id');
    }

    public function charge_option()
    {
        return $this->hasOne(ShareChargeOption::class, 'id', 'share_charge_option_id');
    }
}
