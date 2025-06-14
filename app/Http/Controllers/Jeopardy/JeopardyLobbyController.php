<?php

namespace App\Http\Controllers\Jeopardy;

use App\Enums\LobbyType;
use App\Http\Controllers\Controller;
use App\Models\Jeopardy\JeopardyLobby;
use App\Models\Lobby\Lobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class JeopardyLobbyController extends Controller
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
            $lobby = $this->lobbyService->createLobby($hostId, LobbyType::JEOPARDY);
            return response()->json(['lobby' => $lobby], 200);
        }catch(\Throwable $e){
            return redirect()->back()->with('error', 'unexpected error: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lobby $lobby)
    {
        try{
            $lobby->load('lobbyable');
            $jeopardyLobby = $lobby->lobbyable;
            if(!$jeopardyLobby || !$jeopardyLobby instanceof JeopardyLobby){
                throw new \Exception("Failed to load jeopardy lobby");
            }
            $jeopardyLobby->load('boardCells');
        }catch(\Throwable $e){
            return Inertia::render('jeopardy/Play', [
                'propLobby' => null,
                'propJeopardyLobby' => null,
                'propErrorMessage' => $e->getMessage(),
            ]);
        }
        return Inertia::render('jeopardy/Play', [
            'propLobby' => $lobby,
            'propJeopardyLobby' => $jeopardyLobby,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JeopardyLobby $jeopardyLobby)
    {
        //
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
