<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Branch\Entities\Branch;
use Modules\User\Entities\User;

class JournalEntry extends Model
{
    protected $table = "journal_entries";
    protected $fillable = [];

    public function chart_of_account()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'chart_of_account_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
