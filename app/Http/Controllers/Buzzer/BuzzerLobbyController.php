<?php

namespace App\Http\Controllers\Buzzer;

use App\Enums\LobbyType;
use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Lobby\Lobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BuzzerLobbyController extends Controller
{

    public function __construct(protected LobbyService $lobbyService){

    }
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
        try{
            $hostId = Auth::id();
            $lobby = $this->lobbyService->createLobby($hostId, LobbyType::BUZZER);
            return response()->json(['lobby' => $lobby], 200);
        }catch(\Throwable $e){
            return response()->json(['message' => 'An unexpected error occurred while creating the lobby.'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lobby $lobby)
    {
        try{
            $lobby->load('lobbyable');
            $buzzerLobby = $lobby->lobbyable;
            if(!$buzzerLobby || !$buzzerLobby instanceof BuzzerLobby){
                throw new \Exception('Buzzer Lobby not found');
            }
            $buzzerLobby->load('buzzer_players');

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
