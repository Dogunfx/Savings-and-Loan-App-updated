<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\Savings\Sidebar;

use Illuminate\Support\Facades\Auth;
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
        $this->menu->dropdown(trans_choice('savings::general.savings', 1), function ($sub) {
            $sub->url('savings', trans_choice('core::general.view', 1) . ' ' . trans_choice('savings::general.savings', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('savings/create', trans_choice('core::general.create', 1) . ' ' . trans_choice('savings::general.savings', 1), ['icon' => 'fa fa-circle-o']);
            $sub->url('savings/product', trans_choice('core::general.manage', 1) . ' ' . trans_choice('savings::general.savings', 1) . ' ' . trans_choice('savings::general.product', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('savings/charge', trans_choice('core::general.manage', 1) . ' ' . trans_choice('savings::general.savings', 1) . ' ' . trans_choice('savings::general.charge', 2), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-bank']);
    }

}