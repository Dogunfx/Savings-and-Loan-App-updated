<?php

namespace Modules\Communication\Entities;

use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    protected $fillable = [];
    public $table="sms_gateways";
}
