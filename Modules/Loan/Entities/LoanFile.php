<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class LoanFile extends Model
{
    protected $fillable = [];
    public $table = "loan_files";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
