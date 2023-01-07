<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\Branch\Sidebar;

use Nwidart\Menus\MenuBuilder;

class AdminSideBar
{
    protected $menu;

    public function __construct(MenuBuilder $menu)
    {
        $this->menu = $menu;
        $this->get_menu();
    }

    function get_menu()
    {
        $this->menu->dropdown(trans_choice('core::general.branch', 2), function ($sub) {
            $sub->url('branch', trans_choice('core::general.view', 1) . ' ' . trans_choice('core::general.branch', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('branch/create', trans_choice('core::general.create', 1) . ' ' . trans_choice('core::general.branch', 1), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-building']);

    }

}