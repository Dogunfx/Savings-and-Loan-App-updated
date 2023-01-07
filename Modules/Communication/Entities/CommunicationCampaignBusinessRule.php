<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;

class CommunicationCampaignBusinessRule extends Model
{
    protected $fillable = [];
    public $table = "communication_campaign_business_rules";
    public $timestamps = false;
}
