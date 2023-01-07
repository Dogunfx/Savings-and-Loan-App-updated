<?php

namespace Modules\Savings\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Savings\Entities\Savings;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CalculateInterest extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'savings:calculate_interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates interest for active savings account and add it to interest earned column';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $monthly_dates = [];
        $biannual_dates = [];
        $annual_dates = [];
        for ($i = 0; $i < 12; $i++) {
            array_push($monthly_dates, ["date" => Carbon::today()->startOfYear()->endOfMonth()->addMonthNoOverflow($i)->format("Y-m-d")]);
            if ($i == 5 || $i == 11) {
                array_push($biannual_dates, ["date" => Carbon::today()->startOfYear()->endOfMonth()->addMonthNoOverflow($i)->format("Y-m-d")]);

            }
            if ($i == 11) {
                array_push($annual_dates, ["date" => Carbon::today()->startOfYear()->endOfMonth()->addMonthNoOverflow($i)->format("Y-m-d")]);

            }
        }
        $monthly_dates = collect($monthly_dates);
        $biannual_dates = collect($biannual_dates);
        $annual_dates = collect($annual_dates);
        $query = Savings::with('transactions')
            ->join('savings_products', 'savings_products.id', 'savings.savings_product_id')
            ->selectRaw("savings.*,savings_products.minimum_balance_for_interest_calculation savings_product_minimum_balance_for_interest_calculation")
            ->where('savings.next_interest_calculation_date', Carbon::today()->format("Y-m-d"))
            ->get();
        foreach ($query as $savings) {
            $interest = $savings->calculated_interest;
            $balance_for_compounding = 0;
            $transactions = $savings->transactions->where('submitted_on', '>', $savings->last_interest_calculation_date)->where('submitted_on', '<=', $savings->next_interest_calculation_date)->groupBy('submitted_on');
            if ($savings->interest_calculation_type == 'daily_balance') {
                $iterator = $transactions->getIterator();
                while ($iterator->valid()) {
                    $current_key = $iterator->key();
                    $balance = $transactions[$current_key]->last()->balance;
                    $iterator->next();
                    $next_key = $iterator->key();
                    if ($next_key) {
                        $days = Carbon::parse($current_key)->diffInDays(Carbon::parse($next_key));
                    } else {
                        $days = 1;
                    }
                    if ($balance > $savings->minimum_balance_for_interest_calculation) {
                        $interest = $interest + (($balance + $interest) * $days * $savings->interest_rate / (100 * 365));
                    }

                }
            }
            if ($savings->interest_calculation_type == 'average_daily_balance') {
                $iterator = $transactions->getIterator();
                $total_days = 0;
                $total_balance = 0;
                while ($iterator->valid()) {
                    $current_key = $iterator->key();
                    $balance = $transactions[$current_key]->last()->balance;
                    $iterator->next();
                    $next_key = $iterator->key();
                    if ($next_key) {
                        $days = Carbon::parse($current_key)->diffInDays(Carbon::parse($next_key));
                    } else {
                        $days = 1;
                    }
                    $total_days = $total_days + $days;
                    $total_balance = $total_balance + $balance;

                }
                $balance = $total_balance / $total_days;
                if ($balance > $savings->minimum_balance_for_interest_calculation) {
                    $interest = $interest + (($balance + $interest) * $total_days * $savings->interest_rate / (100 * 365));
                }
            }
            if ($savings->compounding_period == 'daily') {
                $savings->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
            }
            if ($savings->compounding_period == 'weekly') {
                $savings->next_interest_calculation_date = Carbon::tomorrow()->endOfWeek()->format("Y-m-d");

            }
            if ($savings->compounding_period == 'monthly') {
                $savings->next_interest_calculation_date = $monthly_dates->where('date', '>', Carbon::today())->first()['date'];
            }
            if ($savings->compounding_period == 'biannual') {
                $savings->next_interest_calculation_date = $biannual_dates->where('date', '>', Carbon::today())->first()['date'];
            }
            if ($savings->compounding_period == 'annually') {
                $savings->next_interest_calculation_date = $annual_dates->where('date', '>', Carbon::today())->first()['date'];
            }

            $savings->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
            $savings->calculated_interest = round($interest, $savings->decimals);
            $savings->save();
        }
        $this->info("Savings interest calculated  successfully");
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
