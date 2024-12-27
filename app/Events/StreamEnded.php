<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class StreamEnded implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $stream;

    /**
     * Create a new event instance.
     *
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('streams');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->stream->id,
            'title' => $this->stream->title,
            'status' => $this->stream->status,
        ];
    }

    public function broadcastAs()
    {
        return 'streamEnded';
 
    }
}