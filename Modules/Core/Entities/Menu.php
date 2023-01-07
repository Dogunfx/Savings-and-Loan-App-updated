<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = "menus";
    protected $fillable = [];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')->orderBy('menu_order', 'asc');
    }
}
