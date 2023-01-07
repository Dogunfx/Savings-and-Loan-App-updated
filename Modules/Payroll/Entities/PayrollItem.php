<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    protected $fillable = [];
    public $table = "payroll_items";
}
