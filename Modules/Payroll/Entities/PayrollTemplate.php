<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;

class PayrollTemplate extends Model
{
    protected $fillable = [];
    public $table = "payroll_templates";

    public function payroll_items()
    {
        return $this->hasMany(PayrollTemplateItem::class, 'payroll_template_id', 'id');
    }
}
