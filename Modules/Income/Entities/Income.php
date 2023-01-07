<?php

namespace Modules\Income\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Branch\Entities\Branch;
use Modules\User\Entities\User;

class Income extends Model
{
    protected $table = 'income';
    protected $fillable = [];

    public function asset_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'asset_chart_of_account_id');
    }

    public function income_chart()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'income_chart_of_account_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function income_type()
    {
        return $this->hasOne(IncomeType::class, 'id', 'income_type_id');
    }
}
