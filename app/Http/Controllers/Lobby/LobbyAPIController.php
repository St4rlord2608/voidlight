<?php

namespace App\Http\Controllers\Lobby;

use App\Enums\LobbyType;
use App\Http\Controllers\Controller;
use App\Models\Lobby\Lobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class LobbyAPIController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hostId' => 'required'
        ]);
        return $this->lobbyService->createLobby($request->get('hostId'), LobbyType::QUIZ_POKER);
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
            $lobby = Lobby::where('lobby_code', $lobbyCode)->firstOrFail()->load('subLobby');
            $subLobby = $lobby->subLobby;
            switch ($subLobby->lobby_type) {
                case LobbyType::QUIZ_POKER:
                    return response()->json(['message' => 'Quiz Poker found'], 200);
                    break;
                case LobbyType::BUZZER:
                    $buzzerLobby = $lobby->buzzer_lobby;
                    if (!$buzzerLobby) {
                        return response()->json(['message' => 'No Buzzer Lobby found for this lobby'], 404);
                    }
                    $buzzerLobby->load('lobby.subLobby');
                    return $buzzerLobby;
                    break;
                case LobbyType::JEOPARDY:
                    return response()->json(['message' => 'Jeopardy found'], 200);
                    break;
            }
        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'Lobby not found.'], 404);
        }catch(\Throwable $e){
            return response()->json(['message' => 'Unexpected error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lobby $lobby)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lobby $lobby)
    {
        //
    }
}
