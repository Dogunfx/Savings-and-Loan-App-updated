<?php

namespace Modules\Savings\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsTransaction;
use Modules\Savings\Events\TransactionUpdated;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ProcessInterest extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'savings:process_interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Posts interest earned';

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
        $query = Savings::join('savings_products', 'savings_products.id', 'savings.savings_product_id')
            ->selectRaw("savings.*,savings_products.accounting_rule,savings_products.interest_on_savings_chart_of_account_id,savings_products.savings_control_chart_of_account_id")
            ->where('savings.next_interest_posting_date', Carbon::today()->format("Y-m-d"))
            ->get();
        foreach ($query as $savings) {
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->branch_id = $savings->branch_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->name = trans_choice('savings::general.apply', 1) . ' ' . trans_choice('savings::general.interest', 1);
            $savings_transaction->savings_transaction_type_id = 11;
            $savings_transaction->submitted_on = $savings->next_interest_posting_date;
            $savings_transaction->created_on = date("Y-m-d");
            $savings_transaction->amount = $savings->calculated_interest;
            $savings_transaction->credit = $savings->calculated_interest;
            $savings_transaction->reversible = 1;
            $savings_transaction->save();
            if ($savings->savings_product->accounting_rule == 'cash') {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->transaction_number = 'S' . $savings_transaction->id;
                $journal_entry->branch_id = $savings->branch_id;
                $journal_entry->currency_id = $savings->currency_id;
                $journal_entry->chart_of_account_id = $savings->savings_product->savings_control_chart_of_account_id;
                $journal_entry->transaction_type = 'savings_interest';
                $journal_entry->date = $savings->next_interest_posting_date;
                $date = explode('-', $savings->next_interest_posting_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $savings->calculated_interest;
                $journal_entry->reference = $savings->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->transaction_number = 'S' . $savings_transaction->id;
                $journal_entry->branch_id = $savings->branch_id;
                $journal_entry->currency_id = $savings->currency_id;
                $journal_entry->chart_of_account_id = $savings->savings_product->interest_on_savings_chart_of_account_id;
                $journal_entry->transaction_type = 'savings_interest';
                $journal_entry->date = $savings->next_interest_posting_date;
                $date = explode('-', $savings->next_interest_posting_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $savings->calculated_interest;
                $journal_entry->reference = $savings->id;
                $journal_entry->save();
            }
            $next_interest_posting_date = '';
            if ($savings->interest_posting_period_type == 'monthly') {
                $next_interest_posting_date = $monthly_dates->where('date', '>=', Carbon::today())->first()['date'];
            }
            if ($savings->interest_posting_period_type == 'biannual') {
                $next_interest_posting_date = $biannual_dates->where('date', '>=', Carbon::today())->first()['date'];
            }
            if ($savings->interest_posting_period_type == 'annually') {
                $next_interest_posting_date = $annual_dates->where('date', '>=', Carbon::today())->first()['date'];
            }
            $savings->next_interest_posting_date = $next_interest_posting_date;
            $savings->last_interest_posting_date = Carbon::today()->format("Y-m-d");
            $savings->calculated_interest = 0;
            $savings->save();
            event(new TransactionUpdated($savings));
        }
        $this->info("Savings interest posted  successfully");
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
