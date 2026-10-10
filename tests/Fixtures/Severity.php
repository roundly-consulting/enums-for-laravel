<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Contracts\HasColor;
use RoundlyConsulting\Enums\Helpers;

enum Severity: int implements HasColor
{
    use Helpers;

    case Low = 1;
    case High = 9;

    public function color(): string
    {
        return $this === self::High ? 'danger' : 'info';
    }
}
