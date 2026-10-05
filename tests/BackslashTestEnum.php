<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * Backed values that need quoting in the `in:` rule and hold a backslash, which Laravel's
 * CSV parser reads as an escape inside quotes.
 */
enum BackslashTestEnum: string
{
    use Helpers;

    case TrailingBackslash = 'C:\a,b\\';
    case EscapedQuote = 'x\"y,z';
    case Plain = 'z';
}
