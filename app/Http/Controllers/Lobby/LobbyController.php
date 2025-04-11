<?php

namespace App\Http\Controllers\Lobby;

use App\Http\Controllers\Controller;
use App\Models\Lobby;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LobbyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        var_dump("was here");
        $lobbyCode = '1234';
        var_dump($lobbyCode);
        /*$lobby = \App\Models\Lobby\Lobby::create([
            'LobbyCode' => $lobbyCode,
            'HostId' => '123'
        ]);
        var_dump($lobby);*/
        return Inertia::render('buzzer/Index');
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
