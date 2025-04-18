<?php

namespace App\Models\Buzzer;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBuzzerPlayer
 */
class BuzzerPlayer extends Model
{
    protected $guarded = [];
    public function buzzer_lobby(){
        return $this->belongsTo(BuzzerLobby::class);
    }

    protected $casts = [
        'text_locked' => 'boolean'
    ];
}
