<?php

namespace App\Http\Controllers\Jeopardy;

use App\Http\Controllers\Controller;
use App\Models\Jeopardy\JeopardyLobby;
use App\Models\Lobby\Lobby;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JeopardyLobbyController extends Controller
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
            $jeopardyLobby = $lobby->jeopardy_lobby;
            $jeopardyLobby->load('boardCells');
            if(!$jeopardyLobby){
                throw new \Exception("Failed to load jeopardy lobby");
            }
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
