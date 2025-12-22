<?php

namespace App\Events;

use App\Models\Tender;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TenderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tender;
    /**
     * Create a new event instance.
     */
    public function __construct(Tender $tender)
    {
        $this->tender = $tender;
    }

}
