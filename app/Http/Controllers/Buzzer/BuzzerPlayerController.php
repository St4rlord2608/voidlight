<?php

namespace App\Http\Controllers\Buzzer;

use App\Events\Buzzer\PlayerChanged;
use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Buzzer\BuzzerPlayer;
use App\Models\Lobby\Lobby;
use App\Models\User;
use Illuminate\Http\Request;

class BuzzerPlayerController extends Controller
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
    public function store(Lobby $lobby, int $userId, Request $request)
    {
        try{
            $buzzerLobby = $lobby->lobbyable;
            if(!$buzzerLobby || !$buzzerLobby instanceof BuzzerLobby){
                return response()->json(['message' => 'No Buzzer Lobby found for this lobby'], 404);
            }
            $buzzerPlayer = BuzzerPlayer::create([
                'user_id' => $userId,
                'buzzer_lobby_id' => $buzzerLobby->id,
            ]);
            $buzzerLobby->load('buzzer_players.user');
            broadcast(new PlayerChanged(
                $buzzerLobby,
                $lobby->lobby_code,
                $userId
            ));
            return $buzzerPlayer;
        }catch(\Throwable $ex){
            return response()->json(['message' => 'An unexpected error occurred while creating the player.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BuzzerPlayer $buzzerPlayer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BuzzerPlayer $buzzerPlayer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lobby $lobby, int $buzzerPlayerId)
    {
        $validated = $request->validate([
            'id' => 'required',
            'requestingUserId' => 'required',
            'name' => 'required',
            'points' => 'required',
            'textLocked' => 'required',
        ]);
        $buzzerPlayer = BuzzerPlayer::where('id', $buzzerPlayerId)->firstOrFail();
        $buzzerPlayer->points = $validated['points'];
        $buzzerPlayer->text_locked = $validated['textLocked'];
        $buzzerPlayer->save();
        $buzzerLobby = $buzzerPlayer->buzzer_lobby->load('buzzer_players');
        broadcast(new PlayerChanged(
            $buzzerLobby,
            $lobby->lobby_code,
            $validated['requestingUserId']
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuzzerPlayer $buzzerPlayer)
    {
        //
    }
}
