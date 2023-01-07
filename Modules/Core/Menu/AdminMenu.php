<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 10:37 PM
 */

namespace Modules\Core\Menu;

use Nwidart\Menus\Facades\Menu;

class AdminMenu
{
    public $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }
}