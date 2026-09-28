<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * Backed values that collide with the `in:` rule's CSV syntax.
 */
enum SeparatorTestEnum: string
{
    use Helpers;

    case Comma = 'a,b';
    case Quote = 'say "hi"';
    case LeadingQuote = '"q';
    case Plain = 'c';
}
