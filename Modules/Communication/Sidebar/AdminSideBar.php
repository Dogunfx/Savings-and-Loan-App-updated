<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\Communication\Sidebar;

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
        $this->menu->dropdown(trans_choice('communication::general.communication', 1), function ($sub) {
            $sub->url('communication/campaign', trans_choice('core::general.view', 1) . ' ' . trans_choice('communication::general.campaign', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('communication/campaign/create', trans_choice('core::general.create', 1) . ' ' . trans_choice('communication::general.campaign', 1), ['icon' => 'fa fa-circle-o']);
            $sub->url('communication/log', trans_choice('core::general.view', 1) . ' ' . trans_choice('communication::general.log', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('communication/sms_gateway', trans_choice('core::general.manage', 1) . ' ' . trans_choice('communication::general.sms', 1) . ' ' . trans_choice('communication::general.gateway', 2), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-envelope']);

    }

}