<?php

namespace App\Models\Lobby;

use App\Enums\LobbyType;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLobby extends Model
{
    /** @use HasFactory<\Database\Factories\\Lobby\SubLobbyFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'lobby_type' => LobbyType::class,
    ];

    public function lobbies(){
        return $this->hasMany(Lobby::class);
    }

    public function questions(){
        return $this->belongsToMany(
            Question::class,
            'lobby_questions_pivot',
            'sub_lobby_id',
            'question_id'
        );
    }
}
