<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Stream;
use Illuminate\Database\Eloquent\Collection;

class StreamStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;

    // Constructor
    public function __construct(Collection $stream)
    {
        $this->stream = $stream;
    }

    // Broadcast Channel
    public function broadcastOn()
    {
        return new Channel('streams');
    }

    // Broadcast Data
    public function broadcastWith()
    {
        return [
            'message' => 'Stream has started!',
            'stream' => $this->stream,
        ];
    }
}
