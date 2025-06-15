<?php

namespace App\Http\Controllers\Buzzer;

use App\Events\Buzzer\PlayerChanged;
use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Buzzer\BuzzerPlayer;
use App\Models\Lobby\Lobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuzzerPlayerBulkController extends Controller
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
    public function update(Request $request, Lobby $lobby)
    {
        $validated = $request->validate([
            'players' => 'required|array|min:1',
            'players.*.id' => 'required|integer|exists:buzzer_players,id',
            'players.*.points' => 'required|integer',
            'players.*.textLocked' => 'required|boolean',
            'requestingUserId' => 'required'
        ]);

        $playerUpdates = $validated['players'];

        $updatedCount = 0;
        $errors = [];

        try{
            DB::transaction(function () use ($playerUpdates, &$updatedCount, &$errors){
                $playerIds = array_column($playerUpdates, 'id');
                $playersToUpdate = BuzzerPlayer::whereIn('id', $playerIds)->get()->keyBy('id');

                foreach($playerUpdates as $updateData){
                    $playerId = $updateData['id'];

                    if($player = $playersToUpdate->get($playerId)){
                        $player->points = $updateData['points'];
                        $player->text_locked = $updateData['textLocked'];
                        $player->save();
                        $updatedCount++;
                    }else{
                        $errors[] = ['id' => $playerId, 'message' => 'Player not found during transaction.'];
                    }
                }
            });
        }catch(\Throwable $e){
            Log::error("Bulk player update failed for lobby {$lobby->lobby_code}: ".$e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Bulk update failed during database transaction.', 'error' => $e->getMessage()], 500);
        }

        if($updatedCount > 0){
            try{
                $buzzerLobby = $lobby->lobbyable;
                if(!$buzzerLobby || !$buzzerLobby instanceof BuzzerLobby){
                    throw new \Exception();
                }
                $buzzerLobby->load('buzzer_players');

                broadcast(new PlayerChanged(
                    $buzzerLobby,
                    $lobby->lobby_code,
                    $validated['requestingUserId']
                ));
            }catch(\Throwable $e){
                Log::error("Broadcasting failed after bulk player update for lobby {$lobby->lobby_code}: ".$e->getMessage(), ['exception' => $e]);
                return response()->json(['message' => 'Broadcasting failed after bulk player update'], 500);
            }
        }

        if(!empty($errors)){
            return response()->json([
                'message' => 'Bulk update partially completed with some unexpected errors.',
                'updated_count' => $updatedCount,
                'errors' => $errors
            ], 207);
        }

        return response()->json([
            'message' => 'Successfully updated {$updatedCount} players.',
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuzzerPlayer $buzzerPlayer)
    {
        //
    }
}
