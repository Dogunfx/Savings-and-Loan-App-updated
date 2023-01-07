<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Branch\Entities\Branch;
use Modules\Core\Entities\Country;
use Modules\Loan\Entities\Loan;
use Modules\Savings\Entities\Savings;
use Modules\User\Entities\User;

class Client extends Model
{
    protected $table = 'clients';
    protected $fillable = [];
    protected $appends = ['name','name_id'];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }
    public function getNameIDAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name.' (#'.$this->id.')';
    }

    public function loan_officer()
    {
        return $this->hasOne(User::class, 'id', 'loan_officer_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
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

    public function client_type()
    {
        return $this->hasOne(ClientType::class, 'id', 'client_type_id');
    }

    public function next_of_kins()
    {
        return $this->hasMany(ClientNextOfKin::class, 'client_id', 'id');
    }

    public function identifications()
    {
        return $this->hasMany(ClientIdentification::class, 'client_id', 'id');
    }

    public function client_users()
    {
        return $this->hasMany(ClientUser::class, 'client_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(ClientFile::class, 'client_id', 'id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'client_id', 'id');
    }

    public function savings()
    {
        return $this->hasMany(Savings::class, 'client_id', 'id');
    }
}
