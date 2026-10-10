<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Contracts\HasColor;
use RoundlyConsulting\Enums\Helpers;

enum Lane implements HasColor
{
    use Helpers;

    case Fast;
    case Slow;

    public function color(): string
    {
        return strtolower($this->name);
    }
}
