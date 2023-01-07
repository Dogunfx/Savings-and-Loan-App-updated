<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/13/19
 * Time: 4:37 PM
 */

namespace Modules\Loan\Sidebar;

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
        $this->menu->dropdown(trans_choice('loan::general.loan', 2), function ($sub) {
            $sub->url('loan', trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.loan', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('loan/application', trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.application', 2), ['icon' => 'fa fa-circle-o']);

            $sub->url('loan/create', trans_choice('core::general.create', 1) . ' ' . trans_choice('loan::general.loan', 1), ['icon' => 'fa fa-circle-o']);
            $sub->url('loan/product', trans_choice('core::general.manage', 1) . ' ' . trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.product', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('loan/charge', trans_choice('core::general.manage', 1) . ' ' . trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.charge', 2), ['icon' => 'fa fa-circle-o']);
            $sub->url('loan/calculator', trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.calculator', 1), ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-money']);
//        $this->menu->dropdown(trans_choice('loan::general.repayment', 2), function ($sub) {
//            $sub->url('repayment', trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.repayment', 2), ['icon' => 'fa fa-circle-o']);
//            //$sub->url('repayment/create', trans_choice('core::general.create', 1) . ' ' . trans_choice('loan::general.repayment', 1), ['icon' => 'fa fa-circle-o']);
//        }, ['icon' => 'fa fa-dollar']);

    }

}