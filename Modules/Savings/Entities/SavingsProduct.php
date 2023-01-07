<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;

class SavingsProduct extends Model
{
    protected $fillable = [];
    public $table = "savings_products";

    public function charges()
    {
        return $this->hasMany(SavingsProductLinkedCharge::class, 'savings_product_id', 'id');
    }
}
