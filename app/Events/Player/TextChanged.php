<?php

namespace App\Events\Player;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TextChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $lobbyCode;
    public int $userId;
    public string $text;

    /**
     * Create a new event instance.
     */
    public function __construct(string $lobbyCode, int $userId, string $text)
    {
        $this->lobbyCode = $lobbyCode;
        $this->userId = $userId;
        $this->text = $text;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('buzzer.'.$this->lobbyCode.'.playerTextChange'),
        ];
    }
}
