<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * A value that already holds U+E000, the code point readable() masks "0" with.
 */
enum PrivateUseTestEnum: string
{
    use Helpers;

    case Masked = "\u{E000}-1";
}
