<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 8/3/19
 * Time: 10:22 AM
 */

namespace Modules\Savings\Widgets;


use Arrilot\Widgets\AbstractWidget;
use Modules\Branch\Entities\Branch;
use Modules\Savings\Charts\SavingsBalanceOverviewChart;
use Modules\Savings\Entities\Savings;

class SavingsBalanceOverview extends AbstractWidget
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
        $chart = new SavingsBalanceOverviewChart();
        $labels = [];
        $data = [];
        foreach (Branch::join("savings", "savings.branch_id", "branches.id")->selectRaw("coalesce(sum(balance_derived),0) balance,branches.name")->groupBy('branches.id')->get() as $key) {
            array_push($labels, $key->name);
            array_push($data, round($key->balance,2));
        }
        $chart->labels(array_values($labels));
        $chart->label(trans_choice('savings::general.amount', 1));
        $chart->displayLegend(false);
        $chart->title(trans_choice('savings::general.savings', 1) . ' ' . trans_choice('savings::general.balance', 1). ' ' . trans_choice('savings::general.overview', 1));
        $chart->dataset(trans_choice('savings::general.savings', 1) . ' ' . trans_choice('savings::general.balance', 1), 'column', array_values($data));
        return theme_view('savings::widgets.savings_balance_overview', [
            'config' => $this->config,
            'chart' => $chart
        ]);
    }
}