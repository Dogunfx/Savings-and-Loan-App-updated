<?php

namespace Modules\Client\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class ClientUser extends Model
{
    protected $table = 'client_users';
    protected $fillable = [];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
