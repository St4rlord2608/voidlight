<?php

namespace App\Http\Controllers\Buzzer;

use App\Events\BuzzerPlayerChanged;
use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerPlayer;
use App\Models\Lobby\Lobby;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BuzzerPlayerAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lobbyCode, $userId, Request $request)
    {
        try{
            $validated = $request->validate([
                'name' => 'required'
            ]);
            $lobby = Lobby::where('lobby_code', $lobbyCode)->firstOrFail();
            $buzzerLobby = $lobby->buzzer_lobby;
            if(!$buzzerLobby){
                return response()->json(['message' => 'No Buzzer Lobby found for this lobby'], 404);
            }
            $buzzerPlayer = BuzzerPlayer::create([
                'user_id' => $userId,
                'buzzer_lobby_id' => $buzzerLobby->id,
                'name' => $validated['name']
            ]);
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
     * Update the specified resource in storage.
     */
    public function update($lobbyCode, $updateUserId, Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'requestingUserId' => 'required',
            'name' => 'required',
            'points' => 'required',
            'textLocked' => 'required'
        ]);
        $buzzerPlayer = BuzzerPlayer::where('id', $validated['id'])->firstOrFail();
        $buzzerPlayer->points = $validated['points'];
        $buzzerPlayer->text_locked = $validated['textLocked'];
        $buzzerPlayer->save();
        $buzzerLobby = $buzzerPlayer->buzzer_lobby->load('buzzer_players');
        $lobby = $buzzerLobby->lobby;
        broadcast(new BuzzerPlayerChanged(
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
