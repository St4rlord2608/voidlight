<?php

namespace App\Models\Jeopardy;

use App\Models\Lobby\Lobby;
use Illuminate\Database\Eloquent\Model;

class JeopardyLobby extends Model
{
    protected $guarded = [];

    public function lobby(){
        return $this->belongsTo(Lobby::class);
    }

    public function boardCells(){
        return $this->hasMany(JeopardyBoardCell::class);
    }

    public function getLobbyCategories(){
        return $this->boardCells()->with('category')->get()
            ->map(function (JeopardyBoardCell $cell) {return $cell->category();})->unique('id')->values();
    }
}
