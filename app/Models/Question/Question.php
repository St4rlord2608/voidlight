<?php

namespace App\Models\Question;

use App\Models\Buzzer\BuzzerLobby;
use App\Models\Lobby\SubLobby;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Question extends Model
{

    public function subLobbies(){
        return $this->belongsToMany(
            SubLobby::class,
            'lobby_questions_pivot',
            'question_id',
            'sub_lobby_id'
        );
    }

    public function clues(){
        return $this->hasMany(Clue::class);
    }

    public function categories(){
        return $this->belongsToMany(
            Category::class,
            'question_categories_pivot',
            'question_id',
            'category_id'
        );
    }
}
