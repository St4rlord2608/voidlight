interface DBBuzzerPlayer {
    id: number;
    user_id: number;
    user: User,
    points: number;
    text_locked: boolean;
    text: string;
}

interface BuzzerPlayer{
    id: number;
    userId: number;
    user: User,
    points: number;
    textLocked: boolean;
    text: string;
}

interface User {
    id: number;
    name: string;
}
