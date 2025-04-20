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

    public function buzzer_lobby(){
        return $this->hasOne(BuzzerLobby::class);
    }

    public function subLobby(){
        return $this->belongsTo(SubLobby::class);
    }
}
