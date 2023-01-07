<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;

class SavingsProductLinkedCharge extends Model
{
    protected $fillable = [];
    public $table = "savings_product_linked_charges";

    public function charge()
    {
        return $this->hasOne(SavingsCharge::class, 'id', 'savings_charge_id');
    }
}
