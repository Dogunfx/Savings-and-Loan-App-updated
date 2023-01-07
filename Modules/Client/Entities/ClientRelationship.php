<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;

class ClientRelationship extends Model
{
    protected $table='client_relationships';
    public $timestamps=false;
    protected $fillable = [];
}
