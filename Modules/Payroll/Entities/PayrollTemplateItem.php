<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;

class PayrollTemplateItem extends Model
{
    protected $fillable = ['payroll_template_id','payroll_item_id'];
    public $table = "payroll_template_items";

    public function payroll_item()
    {
        return $this->hasOne(PayrollItem::class, 'id', 'payroll_item_id');
    }
}
