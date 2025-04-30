<?php
namespace App\Enums;

enum LobbyType: string
{
    case BUZZER = 'buzzer';
    case QUIZ_POKER = 'quiz_poker';
    case JEOPARDY = 'jeopardy';

    public function label(): string
    {
        return match ($this) {
            self::BUZZER => ('Buzzer'),
            self::QUIZ_POKER => ('Quiz Poker'),
            self::JEOPARDY => ('Jeopardy'),
        };
    }
}
