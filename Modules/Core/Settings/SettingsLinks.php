<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/14/19
 * Time: 2:09 PM
 */

namespace Modules\Core\Settings;
class SettingsLinks
{
    protected $links;

    public function __construct()
    {

    }

    function get_links()
    {
        return [
            "currency" => trans_choice('core::general.currency', 2),
            "payment_type" => trans_choice('core::general.payment', 1) . ' ' . trans_choice('core::general.type', 2),
        ];

    }

}