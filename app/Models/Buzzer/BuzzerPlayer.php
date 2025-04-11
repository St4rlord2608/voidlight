<?php

namespace App\Models\Buzzer;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBuzzerPlayer
 */
class BuzzerPlayer extends Model
{
    protected $guarded = [];
    public function buzzerLobby(){
        return $this->belongsTo(BuzzerLobby::class);
    }
}
