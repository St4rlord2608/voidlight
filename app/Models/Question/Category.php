<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function questions(){
        return $this->belongsToMany(
            Question::class,
            'question_categories_pivot',
            'category_id',
            'question_id'
        );
    }
}
