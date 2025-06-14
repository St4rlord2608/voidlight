<?php

namespace App\Models\Buzzer;

use App\Models\User;
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

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function name(){

    }

    protected $casts = [
        'text_locked' => 'boolean'
    ];
}
