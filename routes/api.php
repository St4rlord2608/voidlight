<?php

use App\Http\Controllers\Broadcast\BuzzerAPIController;
use App\Http\Controllers\Broadcast\PlayerTextAPIController;
use App\Http\Controllers\Buzzer\BuzzerLobbyAPIController;
use App\Http\Controllers\Jeopardy\JeopardyBoardCellAPIController;
use App\Http\Controllers\Jeopardy\JeopardyLobbyAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', function (Request $request) {
    return 'products';
});

Route::get('/jeopardy/{lobbyCode}', [JeopardyLobbyAPIController::class, 'show']);
Route::post('/jeopardy/{lobbyCode}/board_cell', [JeopardyBoardCellAPIController::class, 'store']);

//Route::patch('/broadcast/playerText/{lobbyCode}/{userId}', [PlayerTextAPIController::class, 'update']);
//Route::patch('/broadcast/buzzer/{lobbyCode}', [BuzzerAPIController::class, 'update']);
