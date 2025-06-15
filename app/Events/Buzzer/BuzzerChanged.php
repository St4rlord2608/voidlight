<?php

namespace App\Events\Buzzer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuzzerChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $lobbyCode;
    public bool $wasBuzzerLock;
    public bool $buzzerResult;
    public bool $buzzerCanceled = false;
    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $lobbyCode, bool $wasBuzzerLock, bool $buzzerResult, bool $buzzerCanceled, int $userId)
    {
        $this->lobbyCode = $lobbyCode;
        $this->wasBuzzerLock = $wasBuzzerLock;
        $this->buzzerResult = $buzzerResult;
        $this->buzzerCanceled = $buzzerCanceled;
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
            new Channel('buzzer.'.$this->lobbyCode.'.buzzerChange'),
        ];
    }
}
