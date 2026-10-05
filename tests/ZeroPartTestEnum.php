<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * String values whose headline has a part that is exactly "0".
 */
enum ZeroPartTestEnum: string
{
    use Helpers;

    case Level = 'level';
    case LevelZero = 'level-0';
    case LevelTen = 'level-10';
    case Version = 'v1_0_0';
    case Room = 'room 0';
    case LeadingZero = '0Floor';
}
