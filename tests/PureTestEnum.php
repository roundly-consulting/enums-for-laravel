<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

enum PureTestEnum
{
    use Helpers;

    case Active;
    case Archived;
}
