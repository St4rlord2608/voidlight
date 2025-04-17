<?php

namespace App\Http\Controllers\Buzzer;

use App\Events\BuzzerLobbyChanged;
use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Lobby\Lobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuzzerLobbyAPIController extends Controller
{
    protected $lobbyService;

    public function __construct(LobbyService $lobbyService){
        $this->lobbyService = $lobbyService;
    }
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
    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'hostId' => 'required'
            ]);
            $buzzerLobby = DB::transaction(function () use ($validated) {
                $lobby = $this->lobbyService->createLobby($validated['hostId']);
                $createdBuzzerLobby = BuzzerLobby::create([
                    'lobby_id' => $lobby->id
                ]);
                if(!$createdBuzzerLobby){
                    throw new \Exception("Failed to create buzzer lobby");
                }
                $createdBuzzerLobby->load('lobby');
                return $createdBuzzerLobby;
            });
            return $buzzerLobby;
        }catch(\Throwable $e){
            return response()->json(['message' => 'An unexpected error occurred while creating the lobby.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lobbyCode, Request $request)
    {
        $request->validate([
            'userId' => 'required',
            'userName' => 'required'
        ]);
        try{
            $lobby = Lobby::where('lobby_code', $lobbyCode)->firstOrFail();
            $buzzerLobby = $lobby->buzzer_lobby;

            if (!$buzzerLobby) {
                return response()->json(['message' => 'No Buzzer Lobby found for this lobby'], 404);
            }

            $buzzerLobby->load('lobby');
            return $buzzerLobby;
        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'Lobby not found.'], 404);
        }catch(\Throwable $e){
            return response()->json(['message' => 'Unexpected error: '.$e->getMessage()], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lobbyCode, Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'userId' => 'required',
            'buzzerLocked' => 'required|boolean',
            'buzzedPlayerId' => 'present'
        ]);
        try{
            $buzzerLobby = BuzzerLobby::where('id', $validated['id'])->firstOrFail();
            $lobby = $buzzerLobby->lobby;
            $hostId = $lobby->host_id;
            if($hostId != $validated['buzzedPlayerId'] && $buzzerLobby->buzzer_locked){
                throw new \Exception("buzzer already locked");
            }
            $buzzerLobby->buzzer_locked = $validated['buzzerLocked'];
            $buzzerLobby->buzzed_player_id = $validated['buzzedPlayerId'];
            $buzzerLobby->save();
            broadcast(new BuzzerLobbyChanged(
                $lobbyCode,
                $validated['userId'],
                $validated['buzzerLocked'],
                $validated['buzzedPlayerId']
            ));
        }catch(\Throwable $e){
            return response()->json(['message' => 'Unexpected error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuzzerLobby $buzzerLobby)
    {
        //
    }
}
