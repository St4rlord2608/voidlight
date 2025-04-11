<?php

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

Route::get('/lobby/{lobbyCode}', [LobbyAPIController::class, 'show']);
