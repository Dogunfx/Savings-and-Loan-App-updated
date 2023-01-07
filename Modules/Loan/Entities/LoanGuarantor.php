<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientRelationship;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;
use Modules\User\Entities\User;

class LoanGuarantor extends Model
{
    protected $fillable = [];
    public $table = "loan_guarantors";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

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
