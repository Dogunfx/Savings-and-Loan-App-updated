<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class CommunicationCampaignLog extends Model
{
    protected $fillable = [];
    public $table = "communication_campaign_logs";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function campaign()
    {
        return $this->hasOne(CommunicationCampaign::class, 'id', 'communication_campaign_id');
    }
}
