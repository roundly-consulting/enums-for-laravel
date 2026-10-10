<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * "auth" misses its grouped key, then headlines to "Auth", which names a translation group.
 */
#[TranslatedLabels]
enum AccessArea: string
{
    use Helpers;

    case Auth = 'auth';
    case Billing = 'billing';
}
