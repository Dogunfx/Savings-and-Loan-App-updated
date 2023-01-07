<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;

class ClientIdentification extends Model
{
    protected $table = 'client_identification';
    protected $fillable = [];

    public function identification_type()
    {
        return $this->hasOne(ClientIdentificationType::class, 'id', 'client_identification_type_id');
    }
}
