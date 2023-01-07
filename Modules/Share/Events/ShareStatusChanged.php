<?php

namespace Modules\Share\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Share\Entities\Share;

class ShareStatusChanged
{
    use SerializesModels;

    public $share;
    public $previous_status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Share $share,$previous_status)
    {
        $this->share=$share;
        $this->previous_status=$previous_status;
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
