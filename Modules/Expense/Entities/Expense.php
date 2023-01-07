<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Branch\Entities\Branch;
use Modules\User\Entities\User;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $fillable = [];

    public function asset_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'asset_chart_of_account_id');
    }

    public function expense_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'expense_chart_of_account_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function expense_type()
    {
        return $this->hasOne(ExpenseType::class, 'id', 'expense_type_id');
    }
}
