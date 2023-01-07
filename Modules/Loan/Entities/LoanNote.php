<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class LoanNote extends Model
{
    protected $fillable = [];
    public $table = "loan_notes";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
