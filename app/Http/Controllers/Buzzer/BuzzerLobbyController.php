<?php

namespace App\Http\Controllers\Buzzer;

use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Lobby\Lobby;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuzzerLobbyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($lobbyCode)
    {
        try{
            $lobby = Lobby::where('lobby_code', $lobbyCode)->firstOrFail();
            $buzzerLobby = $lobby->buzzer_lobby;
            $buzzerLobby->load('buzzer_players');
            if(!$buzzerLobby){
                throw new \Exception('Buzzer Lobby not found');
            }
        }catch(\Throwable $ex){
            return Inertia::render('buzzer/Play', [
                'propLobby' => null,
                'propBuzzerLobby' => null,
                'propErrorMessage' => 'error'
            ]);
        }
        return Inertia::render('buzzer/Play', [
            'propLobby' => $lobby,
            'propBuzzerLobby' => $buzzerLobby
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BuzzerLobby $buzzerLobby)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BuzzerLobby $buzzerLobby)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuzzerLobby $buzzerLobby)
    {
        //
    }
}
