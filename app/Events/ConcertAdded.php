<?php

namespace App\Events;

use App\Models\Concert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ConcertAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The concert that was added.
     *
     * @var \App\Models\Concert
     */
    public $concert;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Concert  $concert
     * @return void
     */
    public function __construct(Concert $concert)
    {
        $this->concert = $concert;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
