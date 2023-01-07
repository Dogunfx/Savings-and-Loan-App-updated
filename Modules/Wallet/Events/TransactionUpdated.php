<?php

namespace Modules\Wallet\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Wallet\Entities\Wallet;

class TransactionUpdated
{
    use SerializesModels;

    public $wallet;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
