<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * A value holding a "." cannot be a translation key: the translator reads the dot as
 * nesting, so 'v1.0' misses even though the file has a 'v1.0' line.
 */
#[TranslatedLabels('fixtures::enums.release')]
enum Release: string
{
    use Helpers;

    case Stable = 'v1.0';
    case Next = 'v2';
}
