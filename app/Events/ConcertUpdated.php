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

class ConcertUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The concert that was added.
     *
     * @var \App\Models\Concert
     */
    public $concert;

    /**
     * The path to the previously stored image if one existed.
     *
     * @var string|null
     */
    public $oldImagePath;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Concert  $concert
     * @param  string|null  $oldImagePath
     * @return void
     */
    public function __construct(Concert $concert, $oldImagePath = null)
    {
        $this->concert = $concert;
        $this->oldImagePath = $oldImagePath;
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
