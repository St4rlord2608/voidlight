<?php

namespace App\Models\Buzzer;

use App\Models\Lobby\Lobby;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBuzzerLobby
 */
class BuzzerLobby extends Model
{
    protected $guarded = [];
    public function buzzer_players(){
        return $this->hasMany(BuzzerPlayer::class);
    }

    public function lobby(){
        return $this->belongsTo(Lobby::class);
    }
}
