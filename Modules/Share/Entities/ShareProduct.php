<?php

namespace Modules\Share\Entities;

use Illuminate\Database\Eloquent\Model;

class ShareProduct extends Model
{
    protected $fillable = [];
    public function charges()
    {
        return $this->hasMany(ShareProductLinkedCharge::class, 'share_product_id', 'id');
    }
}
