<?php

namespace Modules\Savings\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Savings\Entities\Savings;

class SavingsStatusChanged
{
    use SerializesModels;
    public $savings;
    public $previous_status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Savings $savings, $previous_status = '')
    {
        $this->savings = $savings;
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
