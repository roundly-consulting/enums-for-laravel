<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * A pure enum: keys use the case name, enums.visibility.Public.
 */
#[TranslatedLabels]
enum Visibility
{
    use Helpers;

    case Public;
    case Private;
}
