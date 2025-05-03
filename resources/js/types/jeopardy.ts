import { Lobby } from '@/types/lobby';
import { Category, DBCategory } from '@/types/Category';
import { DBQuestion, Question } from '@/types/Question';

export interface JeopardyLobby{
    id: number,
    lobby: Lobby,
    JeopardyBoardCells: JeopardyBoardCell[]
}

export interface DBJeopardyBoardCell{
    id: number,
    category: DBCategory,
    question: DBQuestion,
    points: number,
    answered: boolean
}

export interface JeopardyBoardCell{
    id: number,
    category: Category,
    question: Question,
    points: number,
    answered: boolean
}

export interface BoardCell{
    points: number;
    question: Question;
    answered: boolean;
}

export interface DBBoardCell{
    points: number;
    question: DBQuestion;
    answered: boolean;
}

export interface BoardColumn{
    category: Category;
    cells: BoardCell[];
}

export interface DBBoardColumn{
    category: DBCategory;
    cells: DBBoardCell[];
}
