<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\ChartOfAccount;

class ExpenseType extends Model
{
    protected $table = 'expense_types';
    protected $fillable = [];

    public function asset_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'asset_chart_of_account_id');
    }

    public function expense_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'expense_chart_of_account_id');
    }
}
