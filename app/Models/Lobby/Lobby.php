<?php

namespace App\Models\Lobby;

use App\Enums\LobbyType;
use App\Models\Buzzer\BuzzerLobby;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperLobby
 */
class Lobby extends Model
{
    protected $guarded = [];

    protected $casts = [
        'lobby_types' => LobbyType::class,
    ];

    public function buzzer_lobby(){
        return $this->hasOne(BuzzerLobby::class);
    }
}
