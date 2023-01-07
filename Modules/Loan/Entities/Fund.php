<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = [];
    public $table = "funds";
    public $timestamps = false;
}
