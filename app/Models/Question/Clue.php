<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class Clue extends Model
{
    public function question(){
        return $this->belongsTo(Question::class);
    }
}
