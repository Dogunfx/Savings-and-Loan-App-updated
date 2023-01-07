<?php

namespace Modules\Branch\Entities;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = "branches";


    public function users()
    {
        return $this->hasMany(BranchUser::class, 'branch_id', 'id');
    }
}
