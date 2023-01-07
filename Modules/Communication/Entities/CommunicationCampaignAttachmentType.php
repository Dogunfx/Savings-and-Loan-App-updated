<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;

class CommunicationCampaignAttachmentType extends Model
{
    protected $fillable = [];
    public $table="communication_campaign_attachment_types";
    public $timestamps=false;
}
