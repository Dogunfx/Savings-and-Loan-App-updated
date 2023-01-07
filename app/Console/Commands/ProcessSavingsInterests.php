<?php

namespace App\Console\Commands;

use App\Helpers\GeneralHelper;
use App\Models\JournalEntry;
use App\Models\Saving;
use App\Models\SavingTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessSavingsInterests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process savings interest';

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
        //Calculate savings interest
        foreach (DB::table('savings')->join('savings_products', 'savings_products.id', 'savings.savings_product_id')->selectRaw("savings.*,savings_products.interest_compounding_period,savings_products.interest_calculation_type,savings_products.minimum_balance,savings_products.interest_rate")->where('savings.next_interest_calculation_date', Carbon::today()->format("Y-m-d"))->get() as $savings) {
            if ($savings->interest_compounding_period == "monthly") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(1)->format("M"))->format("Y-m-d");
            }
            if ($savings->interest_compounding_period == "quarterly") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(2)->format("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(2))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d");
            }
            if ($savings->interest_compounding_period == "biannual") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(5)->format("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(5))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(6)->format("M"))->format("Y-m-d");

            }
            if ($savings->interest_compounding_period == "annually") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(11)->format("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(11))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(12)->format("M"))->format("Y-m-d");
            }
            //calculate interest using daily balance
            if ($savings->interest_calculation_type == "daily") {
                if ($savings->interest_compounding_period == "daily") {
                    if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
                        $start_date = $savings->start_interest_calculation_date;
                    } else {
                        $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
                    }
                    $total_balance = GeneralHelper::savings_account_balance($savings->id, $start_date) + $savings->interest_earned;
                    $today_balance = SavingTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $savings->id)->where('reversed', 0)->where('date', Carbon::today()->format("Y-m-d"))->first();
                    if (!empty($today_balance)) {
                        $total_balance = $today_balance->balance + $total_balance;
                    }
                    if ($total_balance >= $savings->minimum_balance) {
                        //calculate interest
                        $interest = $total_balance * ($savings->interest_rate / (100 * 365));
                        $savings_to_save = Saving::find($savings->id);
                        $savings_to_save->interest_earned = $savings->interest_earned + $interest;
                        $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
                        $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                        $savings_to_save->save();
                    } else {
                        $savings_to_save = Saving::find($savings->id);
                        $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
                        $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                        $savings_to_save->save();
                    }
                } else {
                    $transactions = SavingTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date'))->where('savings_id', $savings->id)->where('reversed', 0)->whereBetween('date', [$start_date, Carbon::today()->format("Y-m-d")])->groupBy('date')->get();
                    $balance = GeneralHelper::savings_account_balance($savings->id, $start_date) + $savings->interest_earned;
                    $interest = 0;
                    $total_days = 0;
                    if (empty($transactions)) {
                        if ($balance >= $savings->minimum_balance) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()->format("Y-m-d")) + 1;
                            $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
                        }
                    } else {
                        foreach ($transactions as $transaction) {
                            if (Carbon::parse($start_date)->eq(Carbon::parse($transaction->date))) {
                                $days = 1;
                            } else {
                                $days = Carbon::parse($start_date)->diffInDays($transaction->date);
                            }
                            if ($balance >= $savings->minimum_balance) {
                                $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
                            }
                            $start_date = Carbon::parse($start_date)->addDays($days)->format("Y-m-d");
                            $balance = $balance + $transaction->balance;
                            $total_days = $total_days + $days;
                        }
                        if (Carbon::parse($start_date)->notEqualTo(Carbon::today())) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()) + 1;
                            if ($balance >= $savings->minimum_balance) {
                                $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + $days;
                        } else {
                            if ($balance >= $savings->minimum_balance) {
                                $interest = $interest + ($balance * $savings->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + 1;
                        }
                    }
                    $savings_to_save = Saving::find($savings->id);
                    $savings_to_save->interest_earned = $savings->interest_earned + $interest;
                    $savings_to_save->next_interest_calculation_date = $next_interest_calculation_date;
                    $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                    $savings_to_save->save();
                }

            }
            //calculate interest using average balance
            if ($savings->interest_calculation_type == "average") {
                if ($savings->interest_compounding_period == "daily") {
                    if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
                        $start_date = $savings->start_interest_calculation_date;
                    } else {
                        $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
                    }
                    $total_balance = GeneralHelper::savings_account_balance($savings->id, $start_date) + $savings->interest_earned;
                    $today_balance = SavingTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $savings->id)->where('reversed', 0)->where('date', Carbon::today()->format("Y-m-d"))->first();
                    if (!empty($today_balance)) {
                        $total_balance = $today_balance->balance + $total_balance;
                    }
                    if ($total_balance >= $savings->minimum_balance) {
                        //calculate interest
                        $savings_to_save = Saving::find($savings->id);
                        $interest = $total_balance * ($savings->interest_rate / (100 * 365));
                        $savings_to_save->interest_earned = $savings->interest_earned + $interest;
                        $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
                        $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                        $savings_to_save->save();
                    } else {
                        $savings_to_save = Saving::find($savings->id);
                        $savings_to_save->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
                        $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                        $savings_to_save->save();
                    }
                } else {
                    $transactions = SavingTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date'))->where('savings_id', $savings->id)->where('reversed', 0)->whereBetween('date', [$start_date, Carbon::today()->format("Y-m-d")])->groupBy('date')->get();
                    $balance = GeneralHelper::savings_account_balance($savings->id, $start_date);
                    $interest = 0;
                    $total_days = 0;
                    if (empty($transactions)) {
                        if ($balance >= $savings->minimum_balance) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()->format("Y-m-d")) + 1;
                            $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
                        }
                    } else {
                        $average_balance = 0;
                        foreach ($transactions as $transaction) {
                            if (Carbon::parse($start_date)->eq(Carbon::parse($transaction->date))) {
                                $days = 1;
                            } else {
                                $days = Carbon::parse($start_date)->diffInDays($transaction->date);
                            }
                            $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
                            $average_balance = $average_balance + ($balance * $days);
                            $start_date = Carbon::parse($start_date)->addDays($days)->format("Y-m-d");
                            $balance = $balance + $transaction->balance;
                            $total_days = $total_days + $days;
                        }
                        if (Carbon::parse($start_date)->notEqualTo(Carbon::today())) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()) + 1;
                            $average_balance = $average_balance + ($balance * $days);
                            if ($balance >= $savings->minimum_balance) {
                                $interest = $interest + ($balance * $days * $savings->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + $days;
                        } else {
                            $average_balance = $average_balance + ($balance * 1);
                            if ($balance >= $savings->minimum_balance) {
                                $interest = $interest + ($balance * $savings->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + 1;
                        }
                        $average_balance = $average_balance / $total_days;
                        if ($average_balance > $savings->minimum_balance) {
                            $interest = $interest + ($average_balance * $total_days * $savings->interest_rate / (100 * 365));
                        }
                    }
                    $savings_to_save = Saving::find($savings->id);
                    $savings_to_save->interest_earned = $savings->interest_earned + $interest;
                    $savings_to_save->next_interest_calculation_date = $next_interest_calculation_date;
                    $savings_to_save->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                    $savings_to_save->save();
                }
            }


        }
        //post interest
        foreach (DB::table('savings')->join('savings_products', 'savings_products.id', 'savings.savings_product_id')->selectRaw("savings.*,savings_products.chart_reference_id,savings_products.accounting_rule,savings_products.chart_expense_interest_id,savings_products.interest_rate,savings_products.interest_posting_period")->where('savings.next_interest_posting_date', Carbon::today()->format("Y-m-d"))->get() as $key) {
            if ($key->interest_earned > 0) {
                $date = date("Y-m-d");
                $savings_transaction = new SavingTransaction();
                $savings_transaction->borrower_id = $key->borrower_id;
                $savings_transaction->branch_id = $key->branch_id;
                $savings_transaction->savings_id = $key->id;
                $savings_transaction->system_interest = 1;
                $savings_transaction->type = "interest";
                $savings_transaction->reversible = 1;
                $savings_transaction->date = date("Y-m-d");
                $savings_transaction->time = date("H:i");
                $date = explode('-', date("Y-m-d"));
                $savings_transaction->year = $date[0];
                $savings_transaction->month = $date[1];
                $savings_transaction->credit = $key->interest_earned;
                $savings_transaction->notes = $key->interest_rate . " Per Annum Interest calculated";
                $savings_transaction->save();
                if ($key->accounting_rule == 'cash_based') {
                    $journal = new JournalEntry();
                    $journal->account_id = $key->chart_reference_id;
                    $journal->branch_id = $savings_transaction->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $savings_transaction->borrower_id;
                    $journal->transaction_type = 'interest';
                    $journal->name = "Savings Interest";
                    $journal->savings_id = $key->id;
                    $journal->credit = $key->interest_earned;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();

                    $journal = new JournalEntry();
                    $journal->account_id = $key->chart_expense_interest_id;
                    $journal->branch_id = $savings_transaction->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $savings_transaction->borrower_id;
                    $journal->transaction_type = 'interest';
                    $journal->name = "Savings Interest";
                    $journal->savings_id = $key->id;
                    $journal->debit = $key->interest_earned;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();
                }
            }
            $savings = Saving::find($key->id);
            if ($key->interest_posting_period == "monthly") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(1)->format("M"))->format("Y-m-d");
            }
            if ($key->interest_posting_period == "quarterly") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d");
            }
            if ($key->interest_posting_period == "biannual") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(6)->format("M"))->format("Y-m-d");
            }
            if ($key->interest_posting_period == "annually") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(12)->format("M"))->format("Y-m-d");
            }
            $savings->interest_earned = 0;
            $savings->last_interest_posting_date = Carbon::today()->format("Y-m-d");
            $savings->save();
        }

        $this->info("Savings interest calculated and posted successfully");
    }
}
