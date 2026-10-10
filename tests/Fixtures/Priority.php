<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * Int-backed, with a negative value: keys are enums.priority.-1, .1 and .5.
 */
#[TranslatedLabels]
enum Priority: int
{
    use Helpers;

    case Debt = -1;
    case Low = 1;
    case Normal = 5;
}
