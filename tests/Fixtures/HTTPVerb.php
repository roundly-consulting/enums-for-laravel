<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * An acronym class name: the default group is enums.h_t_t_p_verb, so such enums pass one.
 */
#[TranslatedLabels]
enum HTTPVerb: string
{
    use Helpers;

    case Get = 'get';
}
