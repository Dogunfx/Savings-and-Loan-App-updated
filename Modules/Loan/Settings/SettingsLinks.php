<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/14/19
 * Time: 2:09 PM
 */

namespace Modules\Loan\Settings;
class SettingsLinks
{
    protected $links;

    public function __construct()
    {

    }

    function get_links()
    {
        return [
            "loan/fund" => trans_choice('loan::general.fund', 2),
            "loan/collateral_type" => trans_choice('loan::general.collateral', 1).' '.trans_choice('core::general.type', 2),
            "loan/purpose" => trans_choice('loan::general.loan', 1).' '.trans_choice('loan::general.purpose', 2),

        ];

    }

}