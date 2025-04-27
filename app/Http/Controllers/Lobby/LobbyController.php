<?php

namespace App\Http\Controllers\Lobby;

use App\Http\Controllers\Controller;
use App\Models\Lobby;
use App\Models\Lobby\SubLobby;
use App\Services\Lobby\LobbyService;
use Illuminate\Http\Request;
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
        //
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
