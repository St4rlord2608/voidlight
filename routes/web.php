<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('countertest', function(){
    return Inertia::render('CounterTest');
});

Route::get('buzzer', function(){
    return Inertia::render('buzzer/Index');
});

Route::get('test', [\App\Http\Controllers\Lobby\LobbyController::class, 'index']);
Route::get('buzzer/{lobbyCode}', [\App\Http\Controllers\Buzzer\BuzzerLobbyController::class, 'show']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
