<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 8/2/19
 * Time: 8:50 PM
 */

namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Entities\Loan;

class LoanStatistics extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $loans = Loan::with('repayment_schedules')->where('status','active')->get();
        return theme_view('loan::widgets.loan_statistics', [
            'config' => $this->config,
            'loans' => $loans
        ]);
    }
}