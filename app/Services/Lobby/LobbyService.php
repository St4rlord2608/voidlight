<?php
namespace App\Services\Lobby;

use App\Enums\LobbyType;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Jeopardy\JeopardyLobby;
use App\Models\Lobby\Lobby;
use App\Models\Lobby\SubLobby;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LobbyService
{
    public function createLobby(string $hostId, LobbyType $lobbyType) : Lobby{
        return DB::transaction(function () use ($hostId, $lobbyType) {
            $lobbyable = match($lobbyType) {
                LobbyType::BUZZER => BuzzerLobby::create(),
                    LobbyType::JEOPARDY => JeopardyLobby::create(),
                    default => throw new \InvalidArgumentException("Unsupported lobby type."),
            };
            $lobbyCode = $this->generateUniqueLobbyCode();

            $lobby = $lobbyable->lobby()->create([
                'host_id' => $hostId,
                'lobby_code' => $lobbyCode,
            ]);
            return $lobby;
        });
    }

    private function generateUniqueLobbyCode(): string{
        $maxAttempts = 10;
        for($i = 0; $i <= $maxAttempts; $i++){
            $lobbyCode = Str::random(10);
            if(!Lobby::where('lobby_code', $lobbyCode)->exists()){
                return $lobbyCode;
            }
        }
        throw new \Exception('Failed to generate unique lobby code after ' . $maxAttempts . ' attempts.');
    }
}
