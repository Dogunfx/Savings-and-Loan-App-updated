<?php

namespace Modules\CustomField\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomFieldMeta extends Model
{
    protected $table = "custom_fields_meta";
    protected $fillable = ['category', 'parent_id', 'custom_field_id', 'value'];
}
