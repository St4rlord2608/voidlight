<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuzzerLobbyChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $lobbyCode;
    public string $userId;
    public bool $buzzerLocked;
    public $buzzedPlayerId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $lobbyCode, string $userId, bool $buzzerLocked, $buzzedPlayerId)
    {
        $this->lobbyCode = $lobbyCode;
        $this->userId = $userId;
        $this->buzzerLocked = $buzzerLocked;
        $this->buzzedPlayerId = $buzzedPlayerId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('buzzer.'.$this->lobbyCode),
        ];
    }
}
