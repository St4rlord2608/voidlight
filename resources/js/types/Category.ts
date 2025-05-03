import { Question } from '@/types/Question';

export interface DBCategory{
    id: number,
    local_id: number | null,
    name: string,
    user_id: string
}

export interface Category{
    id: number,
    name: string
}
