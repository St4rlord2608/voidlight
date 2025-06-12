<?php

use App\Http\Controllers\Auth\GuestController;
use App\Http\Controllers\Buzzer\BuzzerLobbyController;
use App\Http\Controllers\Jeopardy\JeopardyLobbyController;
use App\Http\Controllers\Lobby\LobbyController;
use App\Http\Controllers\Question\QuestionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function(){
    return Inertia::render('auth/Login');
})->middleware('guest')->name('login');

Route::post('/guest', [GuestController::class, 'store'])->name('guest.login');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Index');
    })->name('home');

    /*Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');*/

    Route::get('buzzer', function(){
        return Inertia::render('buzzer/Index');
    });
    Route::get('join', [LobbyController::class, 'index']);

    Route::get('buzzer/{lobbyCode}', [BuzzerLobbyController::class, 'show']);
    Route::get('jeopardy/{lobbyCode}', [JeopardyLobbyController::class, 'show']);

    Route::get('questions', [QuestionController::class, 'index']);
    Route::get('question/new', [QuestionController::class, 'create']);
    Route::get('question/{questionId}', [QuestionController::class, 'edit']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
