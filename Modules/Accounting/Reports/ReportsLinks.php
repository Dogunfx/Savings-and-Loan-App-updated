<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/14/19
 * Time: 2:09 PM
 */

namespace Modules\Accounting\Reports;
class ReportsLinks
{
    protected $links;

    public function __construct()
    {

    }

    function get_links()
    {
        return [
            "report/accounting" => trans_choice('accounting::general.accounting', 1) . ' ' . trans_choice('report::general.report', 2),
        ];

    }

}