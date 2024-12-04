<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaldoUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $saldo;
    /**
     * Create a new event instance.
     */
    public function __construct($saldo)
    {
        $this->saldo = $saldo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        new Channel('dashboard-saldo');
    }

    public function broadcastWith()
    {
        return [
            'saldo' => $this->saldo,
        ];
    }
}
