<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\Country;

class ClientNextOfKin extends Model
{
    protected $table = 'client_next_of_kin';
    protected $fillable = [];

    public function title()
    {
        return $this->hasOne(Title::class, 'id', 'title_id');
    }

    public function profession()
    {
        return $this->hasOne(Profession::class, 'id', 'profession_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function client_relationship()
    {
        return $this->hasOne(ClientRelationship::class, 'id', 'client_relationship_id');
    }
}
