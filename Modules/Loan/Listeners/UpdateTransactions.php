<?php

namespace Modules\Loan\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Loan\Entities\LoanTransaction;

class UpdateTransactions
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $loan = $event->loan;
        $repayment_schedules = $loan->repayment_schedules;
        $original_transactions = LoanTransaction::where('loan_id', $loan->id)->whereIn('loan_transaction_type_id', [2, 6, 8])->orderBy('submitted_on', 'asc')->orderBy('id', 'asc')->get();
        $transactions = LoanTransaction::where('loan_id', $loan->id)->whereIn('loan_transaction_type_id', [2, 6, 8])->orderBy('submitted_on', 'asc')->orderBy('id', 'asc')->get();
        //set paid derived to zero in repayment schedules
        foreach ($repayment_schedules as &$repayment_schedule) {
            $repayment_schedule->total_due = ($repayment_schedule->principal - $repayment_schedule->principal_written_off_derived - $repayment_schedule->principal_repaid_derived) + ($repayment_schedule->interest - $repayment_schedule->interest_written_off_derived - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived) + ($repayment_schedule->fees - $repayment_schedule->fees_written_off_derived - $repayment_schedule->fees_repaid_derived - $repayment_schedule->fees_waived_derived) + ($repayment_schedule->penalties - $repayment_schedule->penalties_written_off_derived - $repayment_schedule->penalties_repaid_derived - $repayment_schedule->penalties_waived_derived);
            $repayment_schedule->principal_repaid_derived = 0;
            $repayment_schedule->fees_repaid_derived = 0;
            $repayment_schedule->interest_repaid_derived = 0;
            $repayment_schedule->penalties_repaid_derived = 0;

            $repayment_schedule->save();
        }
        foreach ($transactions as &$transaction) {
            $amount = $transaction->amount;
            $principal_repaid_derived = 0;
            $interest_repaid_derived = 0;
            $fees_repaid_derived = 0;
            $penalties_repaid_derived = 0;
            //loop through repayment schedules
            foreach ($repayment_schedules as &$repayment_schedule) {
                if ($amount <= 0) {
                    break;
                }
                $principal = $repayment_schedule->principal - $repayment_schedule->principal_written_off_derived - $repayment_schedule->principal_repaid_derived;
                $interest = $repayment_schedule->interest - $repayment_schedule->interest_written_off_derived - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived;
                $fees = $repayment_schedule->fees - $repayment_schedule->fees_written_off_derived - $repayment_schedule->fees_repaid_derived - $repayment_schedule->fees_waived_derived;
                $penalties = $repayment_schedule->penalties - $repayment_schedule->penalties_written_off_derived - $repayment_schedule->penalties_repaid_derived - $repayment_schedule->penalties_waived_derived;
                $due = $principal + $interest + $fees + $penalties;
                if ($due <= 0) {
                    continue;
                }
                //allocate the payment
                if ($loan->loan_transaction_processing_strategy_id == 1) {
                    //penalties
                    if ($amount >= $penalties && $penalties > 0) {
                        $repayment_schedule->penalties_repaid_derived = $repayment_schedule->penalties_repaid_derived + $penalties;
                        $penalties_repaid_derived = $penalties_repaid_derived + $penalties;
                        $amount = $amount - $penalties;
                    } elseif ($amount < $penalties && $penalties > 0) {
                        $repayment_schedule->penalties_repaid_derived = $repayment_schedule->penalties_repaid_derived + $amount;
                        $penalties_repaid_derived = $penalties_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //fees
                    if ($amount >= $fees && $fees > 0) {
                        $repayment_schedule->fees_repaid_derived = $repayment_schedule->fees_repaid_derived + $fees;
                        $fees_repaid_derived = $fees_repaid_derived + $fees;
                        $amount = $amount - $fees;
                    } elseif ($amount < $fees && $fees > 0) {
                        $repayment_schedule->fees_repaid_derived = $repayment_schedule->fees_repaid_derived + $amount;
                        $fees_repaid_derived = $fees_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //interest
                    if ($amount >= $interest && $interest > 0) {
                        $repayment_schedule->interest_repaid_derived = $repayment_schedule->interest_repaid_derived + $interest;
                        $interest_repaid_derived = $interest_repaid_derived + $interest;
                        $amount = $amount - $interest;
                    } elseif ($amount < $interest && $interest > 0) {
                        $repayment_schedule->interest_repaid_derived = $repayment_schedule->interest_repaid_derived + $amount;
                        $interest_repaid_derived = $interest_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //principal
                    if ($amount >= $principal && $principal > 0) {
                        $repayment_schedule->principal_repaid_derived = $repayment_schedule->principal_repaid_derived + $principal;
                        $principal_repaid_derived = $principal_repaid_derived + $principal;
                        $amount = $amount - $principal;

                    } elseif ($amount < $principal && $principal > 0) {
                        $repayment_schedule->principal_repaid_derived = $repayment_schedule->principal_repaid_derived + $amount;
                        $principal_repaid_derived = $principal_repaid_derived + $amount;
                        $amount = 0;
                    }

                }
                if ($loan->loan_transaction_processing_strategy_id == 2) {

                    //principal
                    if ($amount >= $principal && $principal > 0) {
                        $repayment_schedule->principal_repaid_derived = $repayment_schedule->principal_repaid_derived + $principal;
                        $principal_repaid_derived = $principal_repaid_derived + $principal;
                        $amount = $amount - $principal;

                    } elseif ($amount < $principal && $principal > 0) {
                        $repayment_schedule->principal_repaid_derived = $repayment_schedule->principal_repaid_derived + $amount;
                        $principal_repaid_derived = $principal_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //interest
                    if ($amount >= $interest && $interest > 0) {
                        $repayment_schedule->interest_repaid_derived = $repayment_schedule->interest_repaid_derived + $interest;
                        $interest_repaid_derived = $interest_repaid_derived + $interest;
                        $amount = $amount - $interest;
                    } elseif ($amount < $interest && $interest > 0) {
                        $repayment_schedule->interest_repaid_derived = $repayment_schedule->interest_repaid_derived + $amount;
                        $interest_repaid_derived = $interest_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //penalties
                    if ($amount >= $penalties && $penalties > 0) {
                        $repayment_schedule->penalties_repaid_derived = $repayment_schedule->penalties_repaid_derived + $penalties;
                        $penalties_repaid_derived = $penalties_repaid_derived + $penalties;
                        $amount = $amount - $penalties;
                    } elseif ($amount < $penalties && $penalties > 0) {
                        $repayment_schedule->penalties_repaid_derived = $repayment_schedule->penalties_repaid_derived + $amount;
                        $penalties_repaid_derived = $penalties_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //fees
                    if ($amount >= $fees && $fees > 0) {
                        $repayment_schedule->fees_repaid_derived = $repayment_schedule->fees_repaid_derived + $fees;
                        $fees_repaid_derived = $fees_repaid_derived + $fees;
                        $amount = $amount - $fees;
                    } elseif ($amount < $fees && $fees > 0) {
                        $repayment_schedule->fees_repaid_derived = $repayment_schedule->fees_repaid_derived + $amount;
                        $fees_repaid_derived = $fees_repaid_derived + $amount;
                        $amount = 0;
                    }

                }
                if ($loan->loan_transaction_processing_strategy_id == 3) {

                    //interest
                    if ($amount >= $interest && $interest > 0) {
                        $repayment_schedule->interest_repaid_derived = $repayment_schedule->interest_repaid_derived + $interest;
                        $interest_repaid_derived = $interest_repaid_derived + $interest;
                        $amount = $amount - $interest;
                    } elseif ($amount < $interest && $interest > 0) {
                        $repayment_schedule->interest_repaid_derived = $repayment_schedule->interest_repaid_derived + $amount;
                        $interest_repaid_derived = $interest_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //principal
                    if ($amount >= $principal && $principal > 0) {
                        $repayment_schedule->principal_repaid_derived = $repayment_schedule->principal_repaid_derived + $principal;
                        $principal_repaid_derived = $principal_repaid_derived + $principal;
                        $amount = $amount - $principal;

                    } elseif ($amount < $principal && $principal > 0) {
                        $repayment_schedule->principal_repaid_derived = $repayment_schedule->principal_repaid_derived + $amount;
                        $principal_repaid_derived = $principal_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //penalties
                    if ($amount >= $penalties && $penalties > 0) {
                        $repayment_schedule->penalties_repaid_derived = $repayment_schedule->penalties_repaid_derived + $penalties;
                        $penalties_repaid_derived = $penalties_repaid_derived + $penalties;
                        $amount = $amount - $penalties;
                    } elseif ($amount < $penalties && $penalties > 0) {
                        $repayment_schedule->penalties_repaid_derived = $repayment_schedule->penalties_repaid_derived + $amount;
                        $penalties_repaid_derived = $penalties_repaid_derived + $amount;
                        $amount = 0;
                    }
                    //fees
                    if ($amount >= $fees && $fees > 0) {
                        $repayment_schedule->fees_repaid_derived = $repayment_schedule->fees_repaid_derived + $fees;
                        $fees_repaid_derived = $fees_repaid_derived + $fees;
                        $amount = $amount - $fees;
                    } elseif ($amount < $fees && $fees > 0) {
                        $repayment_schedule->fees_repaid_derived = $repayment_schedule->fees_repaid_derived + $amount;
                        $fees_repaid_derived = $fees_repaid_derived + $amount;
                        $amount = 0;
                    }
                }
                if (($repayment_schedule->principal - $repayment_schedule->principal_written_off_derived - $repayment_schedule->principal_repaid_derived) + ($repayment_schedule->interest - $repayment_schedule->interest_written_off_derived - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived) + ($repayment_schedule->fees - $repayment_schedule->fees_written_off_derived - $repayment_schedule->fees_repaid_derived - $repayment_schedule->fees_waived_derived) + ($repayment_schedule->penalties - $repayment_schedule->penalties_written_off_derived - $repayment_schedule->penalties_repaid_derived - $repayment_schedule->penalties_waived_derived) <= 0) {
                    $repayment_schedule->paid_by_date = $transaction->submitted_on;
                }
                $repayment_schedule->total_due=($repayment_schedule->principal - $repayment_schedule->principal_written_off_derived - $repayment_schedule->principal_repaid_derived)+($repayment_schedule->interest - $repayment_schedule->interest_written_off_derived - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived)+($repayment_schedule->fees - $repayment_schedule->fees_written_off_derived - $repayment_schedule->fees_repaid_derived - $repayment_schedule->fees_waived_derived)+($repayment_schedule->penalties - $repayment_schedule->penalties_written_off_derived - $repayment_schedule->penalties_repaid_derived - $repayment_schedule->penalties_waived_derived);
                $repayment_schedule->save();
                if ($amount <= 0) {
                    break;
                }
            }
            $transaction->principal_repaid_derived = $principal_repaid_derived;
            $transaction->interest_repaid_derived = $interest_repaid_derived;
            $transaction->fees_repaid_derived = $fees_repaid_derived;
            $transaction->penalties_repaid_derived = $penalties_repaid_derived;
            $transaction->save();
            if ($amount <= 0) {
                continue;
            }
        }

        //echo json_encode($transactions);
        $unchanged_transactions = [];
        foreach ($original_transactions as $key) {
            array_push($unchanged_transactions, [
                $key->id,
                $key->loan_id,
                $key->payment_detail_id,
                $key->amount,
                $key->principal_repaid_derived,
                $key->interest_repaid_derived,
                $key->fees_repaid_derived,
                $key->penalties_repaid_derived,
                $key->submitted_on,
            ]);
        }
        $changed_transactions = [];
        $count = 1;
        foreach ($transactions as $key) {
            array_push($changed_transactions, [
                $key->id,
                $key->loan_id,
                $key->payment_detail_id,
                $key->amount,
                $key->principal_repaid_derived,
                $key->interest_repaid_derived,
                $key->fees_repaid_derived,
                $key->penalties_repaid_derived,
                $key->submitted_on,
            ]);
            $count++;
        }
        $transactions_to_be_updated = compare_multi_dimensional_array($changed_transactions, $unchanged_transactions);
        foreach ($transactions_to_be_updated as $key => $value) {
            $transaction = $unchanged_transactions[$key];
            //check if accounting is enabled
            if ($loan->loan_product->accounting_rule == "cash" || $loan->loan_product->accounting_rule == "accrual_periodic" || $loan->loan_product->accounting_rule == "accrual_upfront") {
                //reverse all journal entries linked to this transactions
                foreach (JournalEntry::where('transaction_number', 'L' . $transaction[0])->get() as $journal_entry) {
                    if ($journal_entry->debit > $journal_entry->credit) {
                        $journal_entry->credit = $journal_entry->debit;
                    } else {
                        $journal_entry->debit = $journal_entry->credit;
                    }
                    $journal_entry->reversed = 1;
                    $journal_entry->save();
                }
                //principal repaid
                if ($transaction[4] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->loan_product->loan_portfolio_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[4];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();
                    //debit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->loan_product->fund_source_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->debit = $transaction[3];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();
                }
                //interest repaid
                if ($transaction[5] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->loan_product->income_from_interest_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[5];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();

                }
                //fees repaid
                if ($transaction[6] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->loan_product->income_from_fees_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[6];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();

                }
                //penalties repaid
                if ($transaction[7] > 0) {
                    //credit account
                    $journal_entry = new JournalEntry();
                    $journal_entry->created_by_id = Auth::id();
                    $journal_entry->payment_detail_id = $transaction[2];
                    $journal_entry->transaction_number = 'L' . $transaction[0];
                    $journal_entry->branch_id = $loan->branch_id;
                    $journal_entry->currency_id = $loan->currency_id;
                    $journal_entry->chart_of_account_id = $loan->loan_product->income_from_penalties_chart_of_account_id;
                    $journal_entry->transaction_type = 'loan_repayment';
                    $journal_entry->date = $transaction[8];
                    $date = explode('-', $transaction[8]);
                    $journal_entry->month = $date[1];
                    $journal_entry->year = $date[0];
                    $journal_entry->credit = $transaction[7];
                    $journal_entry->reference = $loan->id;
                    $journal_entry->save();

                }
            }
        }

    }
}
