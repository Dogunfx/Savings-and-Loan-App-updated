<?php

namespace Modules\Savings\Entities;

use Illuminate\Database\Eloquent\Model;

class SavingsLinkedCharge extends Model
{
    protected $fillable = [];
    public $table = "savings_linked_charges";

    public function charge()
    {
        return $this->hasOne(SavingsCharge::class, 'id', 'savings_charge_id');
    }
    public function savings()
    {
        return $this->belongsTo(Savings::class, 'savings_id', 'id');
    }
}
