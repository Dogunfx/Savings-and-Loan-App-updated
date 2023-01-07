<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;

class PayrollItemMeta extends Model
{
    protected $fillable = [];
    public $table = "payroll_items_meta";
}
