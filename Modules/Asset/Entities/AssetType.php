<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    protected $fillable = [];
    protected $table = 'asset_types';
    public $timestamps=false;
}
