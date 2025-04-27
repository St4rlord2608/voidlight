<?php

namespace App\Models\Jeopardy;

use App\Models\Question\Category;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;

class JeopardyBoardCell extends Model
{
    protected $guarded = [];

    protected $casts = [
        'answered' => 'boolean',
        'points' => 'integer',
    ];

    public function jeopardyLobby(){
        return $this->belongsTo(JeopardyLobby::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function question(){
        return $this->belongsTo(Question::class);
    }
}
