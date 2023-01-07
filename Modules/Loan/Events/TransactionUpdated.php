<?php

namespace Modules\Loan\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Loan\Entities\Loan;

class TransactionUpdated
{
    use SerializesModels;
    public $loan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
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
