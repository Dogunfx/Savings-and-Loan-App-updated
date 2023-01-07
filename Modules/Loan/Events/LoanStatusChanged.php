<?php

namespace Modules\Loan\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Loan\Entities\Loan;

class LoanStatusChanged
{
    use SerializesModels;
    public $loan;
    public $previous_status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Loan $loan, $previous_status = '')
    {
        $this->loan = $loan;
        $this->previous_status = $previous_status;
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
