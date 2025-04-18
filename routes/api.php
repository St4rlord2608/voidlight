<?php

use App\Http\Controllers\Broadcast\PlayerTextAPIController;
use App\Http\Controllers\Buzzer\BuzzerLobbyAPIController;
use App\Http\Controllers\Buzzer\BuzzerPlayerAPIController;
use App\Http\Controllers\Buzzer\BuzzerPlayerBulkAPIController;
use App\Http\Controllers\Lobby\LobbyAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', function (Request $request) {
    return 'products';
});

Route::get('/lobbies', [LobbyAPIController::class, 'index']);

Route::post('/lobby', [LobbyAPIController::class, 'store']);

Route::post('/buzzer', [BuzzerLobbyAPIController::class, 'store']);

Route::get('/buzzer/{lobbyCode}', [BuzzerLobbyAPIController::class, 'show']);
Route::patch('/buzzer/{lobbyCode}', [BuzzerLobbyAPIController::class, 'update']);

Route::post('/buzzer/{lobbyCode}/{userId}', [BuzzerPlayerAPIController::class, 'store']);
Route::patch('/buzzer/{lobbyCode}/{updateUserId}', [BuzzerPlayerAPIController::class, 'update']);
Route::patch('/lobby/{lobbyCode}/users/bulk-update', [BuzzerPlayerBulkAPIController::class, 'update']);

Route::get('/lobby/{lobbyCode}', [LobbyAPIController::class, 'show']);

Route::patch('/broadcast/playerText/{lobbyCode}/{userId}', [PlayerTextAPIController::class, 'update']);
