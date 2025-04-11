<?php

namespace App\Models\Lobby;

use App\Models\Buzzer\BuzzerLobby;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperLobby
 */
class Lobby extends Model
{
    protected $guarded = [];
    public function buzzerLobbies(){
        return $this->hasOne(BuzzerLobby::class);
    }
}
