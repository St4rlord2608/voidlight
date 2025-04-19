<?php

namespace App\Http\Controllers\Lobby;

use App\Enums\LobbyType;
use App\Http\Controllers\Controller;
use App\Models\Lobby\Lobby;
use App\Services\Lobby\LobbyService;
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
    public function show(Lobby $lobby)
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
