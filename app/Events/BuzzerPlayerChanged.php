<?php

namespace App\Events;

use App\Models\Buzzer\BuzzerLobby;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuzzerPlayerChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public BuzzerLobby $buzzerLobby;
    public string $lobbyCode;
    public string $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(BuzzerLobby $buzzerLobby, string $lobbyCode, string $userId)
    {
        $this->buzzerLobby = $buzzerLobby;
        $this->lobbyCode = $lobbyCode;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('buzzer.'.$this->lobbyCode.'.playerChange'),
        ];
    }
}
