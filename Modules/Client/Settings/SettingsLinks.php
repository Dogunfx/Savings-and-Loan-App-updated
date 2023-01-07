<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/14/19
 * Time: 2:09 PM
 */

namespace Modules\Client\Settings;
class SettingsLinks
{
    protected $links;

    public function __construct()
    {

    }

    function get_links()
    {
        return [
            "client/client_type" => trans_choice('client::general.client', 1) . ' ' . trans_choice('core::general.type', 2),
            "client/title" => trans_choice('client::general.title', 2),
            "client/profession" => trans_choice('client::general.profession', 2),
            "client/client_relationship" => trans_choice('client::general.client', 1) . ' ' . trans_choice('client::general.relationship', 2),
            "client/client_identification_type" => trans_choice('client::general.client', 1) . ' ' . trans_choice('client::general.identification', 1) . ' ' . trans_choice('core::general.type', 2),
        ];

    }

}