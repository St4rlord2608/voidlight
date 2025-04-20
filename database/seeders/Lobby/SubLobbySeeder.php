<?php

namespace Database\Seeders\Lobby;

use App\Enums\LobbyType;
use App\Models\Lobby\SubLobby;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubLobbySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (LobbyType::cases() as $lobbyType) {

            SubLobby::updateOrCreate(
                ['lobby_type' => $lobbyType],
                ['lobby_type' => $lobbyType]
            );
        }
    }
}
