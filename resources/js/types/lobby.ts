import { SubLobby } from '@/types/subLobby';

export interface Lobby {
    id: number,
    host_id: string,
    lobby_code: string,
    sub_lobby: SubLobby
}
