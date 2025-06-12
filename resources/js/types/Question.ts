import { Category } from '@/types/Category';

export interface DBQuestion{
    id: number,
    local_id: number | null,
    answer: string,
    question: string,
    user_id: string
}

export interface Question{
    id: number,
    answer: string,
    question: string,
    categoryIds: number[],
    lobbyTypes: string[],
    clues: Clue[]
}

export interface Clue{
    value: string,
    order: number
}

export interface QuestionsData{
    nextCategoryId: number,
    nextQuestionId: number,
    categories: Category[],
    questions: Question[]
}
