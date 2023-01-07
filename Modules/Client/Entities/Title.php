<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $table='titles';
    public $timestamps=false;
    protected $fillable = [];
}
