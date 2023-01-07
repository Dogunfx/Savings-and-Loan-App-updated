<?php

namespace App\Listeners;

use App\Events\UpdateLoanTransactions;
use App\Models\JournalEntry;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ProcessUpdateLoanTransactions
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
     * @param  UpdateLoanTransactions $event
     * @return void
     */
    public function handle(UpdateLoanTransactions $event)
    {
        $loan = $event->loan;
        $loan_product = $loan->loan_product;
        DB::table("loan_schedules")->where('loan_id', $loan->id)->update(["principal_paid" => 0, "interest_paid" => 0, "fees_paid" => 0, "penalty_paid" => 0, "paid_by_date" => null]);
        $exclude_schedules = [];
        foreach (LoanTransaction::where('transaction_type',
            'repayment')->where('reversed', 0)->where('loan_id', $loan->id)->orderBy('date', 'asc')->get() as $loan_transaction) {
            $principal_paid = 0;
            $interest_paid = 0;
            $fees_paid = 0;
            $penalty_paid = 0;
            $payments = $loan_transaction->credit;
            foreach (LoanSchedule::where('loan_id', $loan->id)->orderBy('id', 'asc')->whereNotIn('id', $exclude_schedules)->get() as $key) {
                $principal_due = $key->principal - $key->principal_paid - $key->principal_waived;
                $interest_due = $key->interest - $key->interest_paid - $key->interest_waived;
                $fees_due = $key->fees - $key->fees_paid - $key->fees_waived;
                $penalty_due = $key->penalty - $key->penalty_paid - $key->penalty_waived;
                $schedule_due = $principal_due + $interest_due + $fees_due + $penalty_due;
                if ($payments <= 0) {
                    break;
                }
                if ($schedule_due <= 0) {
                    array_push($exclude_schedules, $key->id);
                    continue;
                } else {
                    $repayment_order = unserialize($loan_product->repayment_order);
                    foreach ($repayment_order as $order) {
                        if ($order == 'interest') {
                            if ($payments >= $interest_due) {
                                $key->interest_paid = $key->interest_paid + $interest_due;
                                $interest_paid = $interest_paid + $interest_due;
                                $payments = $payments - $interest_due;
                                $schedule_due = $schedule_due - $interest_due;

                            } else {
                                $key->interest_paid = $key->interest_paid + $payments;
                                $interest_paid = $interest_paid + $payments;
                                $schedule_due = $schedule_due - $payments;
                                $payments = 0;
                                break;
                            }
                        }
                        if ($order == 'penalty') {
                            if ($payments >= $penalty_due) {
                                $key->penalty_paid = $key->penalty_paid + $penalty_due;
                                $penalty_paid = $penalty_paid + $penalty_due;
                                $schedule_due = $schedule_due - $penalty_due;
                                $payments = $payments - $penalty_due;

                            } else {
                                $key->penalty_paid = $key->penalty_paid + $payments;
                                $penalty_paid = $penalty_paid + $payments;
                                $schedule_due = $schedule_due - $payments;
                                $payments = 0;
                                break;
                            }

                        }
                        if ($order == 'fees') {
                            if ($payments >= $fees_due) {
                                $key->fees_paid = $key->fees_paid + $fees_due;
                                $fees_paid = $fees_paid + $fees_due;
                                $schedule_due = $schedule_due - $fees_due;
                                $payments = $payments - $fees_due;

                            } else {
                                $key->fees_paid = $key->fees_paid + $payments;
                                $fees_paid = $fees_paid + $payments;
                                $schedule_due = $schedule_due - $payments;
                                $payments = 0;
                                break;
                            }

                        }
                        if ($order == 'principal') {
                            if ($payments >= $principal_due) {
                                $key->principal_paid = $key->principal_paid + $principal_due;
                                $principal_paid = $principal_paid + $principal_due;
                                $schedule_due = $schedule_due - $principal_due;
                                $payments = $payments - $principal_due;

                            } else {
                                $key->principal_paid = $key->principal_paid + $payments;
                                $principal_paid = $principal_paid + $payments;
                                $schedule_due = $schedule_due - $payments;
                                $payments = 0;
                                break;
                            }
                        }
                    }
                    $key->save();
                    if ($schedule_due <= 0) {
                        array_push($exclude_schedules, $key->id);
                        //add paid by date
                        $key->paid_by_date = $loan_transaction->date;
                        $key->save();
                    }
                    if ($payments <= 0) {
                        break;
                    }
                }
            }
            //update loan transaction
            $loan_transaction->principal_paid = $principal_paid;
            $loan_transaction->interest_paid = $interest_paid;
            $loan_transaction->penalty_paid = $penalty_paid;
            $loan_transaction->fees_paid = $fees_paid;
            $loan_transaction->save();
            //update journal entries
            $date = explode('-', $loan_transaction->date);
            if ($principal_paid > 0) {
                if (!empty($loan->loan_product->chart_loan_portfolio)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_principal';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $principal_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_fund_source)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_fund_source->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $principal_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //interest
            if ($interest_paid > 0) {
                if (!empty($loan->loan_product->chart_income_interest)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_income_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_interest';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $interest_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_interest)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $interest_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //fees
            if ($fees_paid > 0) {
                if (!empty($loan->loan_product->chart_income_fee)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_income_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_fees';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $fees_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_fee)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $fees_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            if ($penalty_paid > 0) {
                if (!empty($loan->loan_product->chart_income_penalty)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_penalty';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $penalty_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_penalty)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                    $journal->date = $loan_transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $penalty_paid;
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
        }
    }
}
