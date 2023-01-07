<?php

namespace Modules\Asset\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Branch\Entities\Branch;

class Asset extends Model
{
    protected $fillable = [];
    protected $table = 'assets';

    public function depreciation()
    {
        return $this->hasMany(AssetDepreciation::class, 'asset_id', 'id')->orderBy('year','desc');
    }

    public function type()
    {
        return $this->hasOne(AssetType::class, 'id', 'asset_type_id');
    }
    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
