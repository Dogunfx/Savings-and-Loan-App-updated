<?php

namespace Modules\Savings\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Savings\Entities\Savings;

class TransactionUpdated
{
    use SerializesModels;
    public $savings;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Savings $savings)
    {
        $this->savings = $savings;
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
