<?php

namespace Modules\Share\Entities;

use Illuminate\Database\Eloquent\Model;

class ShareProductLinkedCharge extends Model
{
    protected $fillable = [];
    public function charge()
    {
        return $this->hasOne(ShareCharge::class, 'id', 'share_charge_id');
    }
}
