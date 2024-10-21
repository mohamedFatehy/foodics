<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $updatedIngredients;

    /**
     * Create a new event instance.
     */
    public function __construct(array $updatedIngredients)
    {
        $this->updatedIngredients = $updatedIngredients;
    }
}
