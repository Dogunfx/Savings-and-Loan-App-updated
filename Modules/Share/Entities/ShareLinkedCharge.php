<?php

namespace Modules\Share\Entities;

use Illuminate\Database\Eloquent\Model;

class ShareLinkedCharge extends Model
{
    protected $fillable = [];

    public function charge()
    {
        return $this->hasOne(ShareCharge::class, 'id', 'share_charge_id');
    }

    public function savings()
    {
        return $this->belongsTo(Share::class, 'share_id', 'id');
    }
}
