<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;

class ClientIdentificationType extends Model
{
    protected $table='client_identification_types';
    public $timestamps=false;
    protected $fillable = [];
}
