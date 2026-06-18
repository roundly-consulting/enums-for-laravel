<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

enum IntTestEnum: int
{
    use Helpers;

    case Low = 0;
    case Medium = 5;
    case High = 10;
}
