<?php

namespace App\Http\Controllers\Jeopardy;

use App\Enums\LobbyType;
use App\Http\Controllers\Controller;
use App\Models\Jeopardy\JeopardyLobby;
use App\Models\Lobby\Lobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JeopardyLobbyAPIController extends Controller
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
        $validated = $request->validate([
            'hostId' => 'required'
        ]);
        try{
            $jeopardyLobby = DB::transaction(function () use ($validated) {
                $lobby = $this->lobbyService->createLobby($validated['hostId'], LobbyType::JEOPARDY);
                $createdJeopardyLobby = JeopardyLobby::create([
                    'lobby_id' => $lobby->id,
                ]);
                if(!$createdJeopardyLobby){
                    throw new \Exception("Failed to create jeopardy");
                }
                $createdJeopardyLobby->load('lobby');
                return $createdJeopardyLobby;
            });
            return $jeopardyLobby;
        }catch (\Throwable $e){
            return response()->json(['message' => 'An unexpected error occured while createing the lobby. '. $e->getMessage()], 500);
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
            $jeopardyLobby = $lobby->jeopardy_lobby;

            if(!$jeopardyLobby){
                return response()->json(['message' => 'No Jeopardy lobby found for this lobby'], 404);
            }

            $jeopardyLobby->load('lobby');
            return $jeopardyLobby;
        }catch (\Throwable $e){
            return response()->json(['message' => 'Lobby not found.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JeopardyLobby $jeopardyLobby)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JeopardyLobby $jeopardyLobby)
    {
        //
    }
}
