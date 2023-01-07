<?php

namespace Modules\Income\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\ChartOfAccount;

class IncomeType extends Model
{
    protected $table = 'income_types';
    protected $fillable = [];

    public function asset_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'asset_chart_of_account_id');
    }

    public function income_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'income_chart_of_account_id');
    }
}
