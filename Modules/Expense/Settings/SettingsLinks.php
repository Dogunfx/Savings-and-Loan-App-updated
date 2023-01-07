<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/14/19
 * Time: 2:09 PM
 */

namespace Modules\Expense\Settings;
class SettingsLinks
{
    protected $links;

    public function __construct()
    {

    }

    function get_links()
    {
        return [
            // "client/client_type" => trans_choice('client::general.client', 1) . ' ' . trans_choice('core::general.type', 2),
        ];

    }

}