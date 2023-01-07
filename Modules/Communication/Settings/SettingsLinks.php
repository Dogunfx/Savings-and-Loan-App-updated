<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/14/19
 * Time: 2:09 PM
 */

namespace Modules\Communication\Settings;
class SettingsLinks
{
    protected $links;

    public function __construct()
    {

    }

    function get_links()
    {
        return [
            "communication/sms_gateway" => trans_choice('communication::general.sms', 1) . ' ' . trans_choice('communication::general.gateway', 2),
        ];

    }

}