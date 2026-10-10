<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Contracts\HasColor;
use RoundlyConsulting\Enums\Helpers;

#[TranslatedLabels]
enum BadgeStatus: string implements HasColor
{
    use Helpers;

    case Draft = 'draft';
    case Live = 'live';
    case Archived = 'archived';

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Live => 'success',
            self::Archived => '#6b7280',
        };
    }
}
