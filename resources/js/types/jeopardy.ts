import { Lobby } from '@/types/lobby';
import { Category } from '@/types/Category';
import { Question } from '@/types/Question';

export interface JeopardyLobby{
    id: number,
    lobby: Lobby,
    JeopardyBoardCells: JeopardyBoardCell[]
}

export interface JeopardyBoardCell{
    id: number,
    category: Category,
    question: Question,
    points: number,
    answered: boolean
}
