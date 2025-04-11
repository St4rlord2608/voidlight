<?php

namespace App\Http\Controllers\Lobby;

use App\Http\Controllers\Controller;
use App\Models\Lobby\Lobby;
use Illuminate\Http\Request;

class LobbyAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd("hello world");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        var_dump("was here");
        $request->validate([
            'HostId' => 'required'
        ]);
        $lobbyCode = $this->CreateValidLobbyCode();
        var_dump($lobbyCode);
        $lobby = Lobby::create([
            'LobbyCode' => $lobbyCode,
            'HostId' => $request->get('HostId')
        ]);
        return $lobby;
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

    private function CreateValidLobbyCode(): string
    {
        $lobbies = Lobby::all();
        $lobbyCode = $this->CreateUncheckedLobbyCode();
        while($lobbies->filter(function($item) use ($lobbyCode){
            return $item->LobbyCode == $lobbyCode;
        })->count() !== 0){
            $lobbyCode = $this->CreateUncheckedLobbyCode();
        }
        return $lobbyCode;
    }

    private function CreateUncheckedLobbyCode(): string
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $lobbyCode = "";
        $maxIndex = strlen($chars) - 1;
        for($i = 0; $i < 10; $i++){
            $randomIndex = random_int(0, $maxIndex);
            $lobbyCode .= $chars[$randomIndex];
        }
        return $lobbyCode;
    }
}
