<?php

namespace Modules\Wallet\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $wallet = $event->wallet;
        $balance = 0;
        foreach ($wallet->transactions as $wallet_transaction) {
            $balance = $balance - $wallet_transaction->debit + $wallet_transaction->credit;
            $wallet_transaction->balance = $balance;
            $wallet_transaction->save();
        }
        $wallet->balance = $balance;
        $wallet->save();
    }
}
