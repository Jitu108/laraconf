<?php

namespace App\Enums;

enum TalkLength: string
{
    case LIGHTENING = 'Lightening - 15 min';
    case NORMAL = 'Normal - 30 min';
    case KEYNOTE = 'Keynote';

    public function getLengthIcon(): string
    {
        return match ($this) {
            self::LIGHTENING => 'heroicon-o-bolt',
            self::NORMAL => 'heroicon-o-megaphone',
            self::KEYNOTE => 'heroicon-o-key'
        };
    }
}
