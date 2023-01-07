<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $table='professions';
    public $timestamps=false;
    protected $fillable = [];
}
