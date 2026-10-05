<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * A negative int and its positive twin: their labels must stay apart.
 */
enum SignedIntTestEnum: int
{
    use Helpers;

    case Minus = -1;
    case Plus = 1;
}
