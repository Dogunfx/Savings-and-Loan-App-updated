<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\Core\Sidebar;

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
        $this->menu->dropdown(trans_choice('setting::general.setting', 2), function ($sub) {
            $sub->url('setting/organisation', trans_choice('setting::general.organisation', 1) . ' ' . trans_choice('setting::general.setting', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('setting/general', trans_choice('setting::general.general', 1) . ' ' . trans_choice('setting::general.setting', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('setting/email', trans_choice('setting::general.email', 1) . ' ' . trans_choice('setting::general.setting', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('setting/sms', trans_choice('setting::general.sms', 1) . ' ' . trans_choice('setting::general.setting', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('setting/system', trans_choice('setting::general.system', 1) . ' ' . trans_choice('setting::general.setting', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('setting/system_update', trans_choice('setting::general.system', 1) . ' ' . trans_choice('setting::general.update', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('setting/other', trans_choice('setting::general.other', 1) . ' ' . trans_choice('setting::general.setting', 2), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-cogs']);
        $this->menu->url('module', trans_choice('core::general.manage', 1) . ' ' . trans_choice('core::general.module', 2), ['icon' => 'fa fa-plug'])->order(99);

    }

}