<?php
namespace App\Services\Lobby;

use App\Enums\LobbyType;
use App\Models\Lobby\Lobby;

class LobbyService
{
    public function createLobby(string $hostId, LobbyType $lobbyType) : Lobby{
        $lobbyCode = $this->CreateValidLobbyCode();
        $lobby = Lobby::create([
            'lobby_code' => $lobbyCode,
            'host_id' => $hostId,
            'lobby_type' => $lobbyType->label()
        ]);
        return $lobby;
    }

    private function CreateValidLobbyCode(): string
    {
        $lobbies = Lobby::all();
        $lobbyCode = $this->CreateUncheckedLobbyCode();
        while($lobbies->filter(function($item) use ($lobbyCode){
                return $item->LobbyCode == $lobbyCode;
            })->count() !== 0){
            $lobbyCode = $this->CreateUncheckedLobbyCode();
        }
        return $lobbyCode;
    }

    private function CreateUncheckedLobbyCode(): string
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $lobbyCode = "";
        $maxIndex = strlen($chars) - 1;
        for($i = 0; $i < 10; $i++){
            $randomIndex = random_int(0, $maxIndex);
            $lobbyCode .= $chars[$randomIndex];
        }
        return $lobbyCode;
    }
}
