<?php

namespace App\Models\Buzzer;

use App\Models\Lobby\Lobby;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin IdeHelperBuzzerLobby
 */
class BuzzerLobby extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'buzzer_locked' => 'boolean'
    ];

    protected $attributes = [
        'settings' => '{
            "show_points": true
        }',
    ];
    public function buzzer_players(){
        return $this->hasMany(BuzzerPlayer::class);
    }

    public function lobby(){
        return $this->belongsTo(Lobby::class);
    }

    public function questions(): MorphToMany{
        return $this->morphToMany(
            Question::class,
            'sub_lobby',
            'lobby_questions_pivot'
        );
    }
}
