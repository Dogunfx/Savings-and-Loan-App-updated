<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class CommunicationCampaign extends Model
{
    protected $fillable = [];
    public $table = "communication_campaigns";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
