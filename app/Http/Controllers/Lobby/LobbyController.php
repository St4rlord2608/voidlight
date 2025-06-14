<?php

namespace App\Http\Controllers\Lobby;

use App\Enums\LobbyType;
use App\Http\Controllers\Controller;
use App\Models\Buzzer\BuzzerLobby;
use App\Models\Jeopardy\JeopardyLobby;
use App\Models\Lobby\Lobby;
use App\Models\Lobby\SubLobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LobbyController extends Controller
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
        $subLobbies = SubLobby::all();
        return Inertia::render('lobby/Create-Join', [
            'subLobbies' => $subLobbies
        ]);
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
    public function show(Lobby $lobby)
    {
        try{
            $user = Auth::user();
            $lobby->load('lobbyable');
            $specificLobby = $lobby->lobbyable;
            $lobbyType = match (true){
                $specificLobby instanceof BuzzerLobby => LobbyType::BUZZER,
                $specificLobby instanceof JeopardyLobby => LobbyType::JEOPARDY,
                default => throw new ModelNotFoundException()
            };
            return response()->json(['lobby_type' => $lobbyType], 200);
        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'Lobby not found.'], 404);
        }catch(\Throwable $e){
            return response()->json(['message' => 'Unexpected error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lobby $lobby)
    {
        //
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
