import { SubLobby } from '@/types/subLobby';

export interface DBLobby {
    id: number,
    host_id: string,
    lobby_code: string,
    sub_lobby: SubLobby
}

export interface Lobby{
    id: number,
    hostId: string,
    lobbyCode: string,
    subLobby: SubLobby
}
