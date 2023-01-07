<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\User\Sidebar;

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
        $this->menu->dropdown(trans_choice('user::general.user', 2), function ($sub) {
            $sub->url('user', trans_choice('core::general.view', 1) . ' ' . trans_choice('core::general.user', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('user/create', trans_choice('core::general.create', 1) . ' ' . trans_choice('core::general.user', 1), ['icon' => 'fa fa-circle-o']);
            $sub->url('user/role', trans_choice('core::general.manage', 1) . ' ' . trans_choice('user::general.role', 2), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-users']);

    }

}