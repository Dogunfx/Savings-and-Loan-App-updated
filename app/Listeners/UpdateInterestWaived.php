<?php

namespace App\Listeners;

use App\Events\InterestWaived;
use App\Events\LoanTransactionUpdated;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateInterestWaived
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InterestWaived $event
     * @return void
     */
    public function handle(InterestWaived $event)
    {
        $loan_transaction = $event->loan_transaction;
        $amount = $loan_transaction->amount;
        foreach (LoanSchedule::where('loan_id', $loan_transaction->loan_id)->orderBy('id', 'asc')->get() as $key) {
            $interest_due = $key->interest - $key->interest_waived - $key->interest_paid;
            if ($interest_due <= 0) {
                continue;
            }
            if ($amount > $interest_due) {
                $key->interest_waived = $key->interest_waived + $interest_due;
                $amount = $amount - $interest_due;
            } else {
                $key->interest_waived = $key->interest_waived + $amount;
                $amount = 0;
            }
            $key->save();
            if ($amount <= 0) {
                break;
            }

        }
        event(new LoanTransactionUpdated($loan_transaction));
    }


}
