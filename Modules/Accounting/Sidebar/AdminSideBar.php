<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\Accounting\Sidebar;

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
        $this->menu->dropdown(trans_choice('accounting::general.accounting', 1), function ($sub) {
            $sub->url('chart_of_account', trans_choice('core::general.view', 1) . ' ' . trans_choice('accounting::general.chart_of_account', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('journal_entry', trans_choice('accounting::general.journal', 1) . ' ' . trans_choice('core::general.entry', 2), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-money']);

    }

}