<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class messageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $chatData ;
    /**
     * Create a new event instance.
     */
    public function __construct($chatData)
    {
        $this->chatData = $chatData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('broadcast-message'),
        ];
    }
    public function broadcastAs() // Aliases 
    {
        return 'getChatMessage';
    }
    public function broadcastWith() // For send any data
    {
        return [
            'chat'=>$this->chatData,
        ];
    }
}
