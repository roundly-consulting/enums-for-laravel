<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * Two values that headline to the same label, "In Progress".
 */
enum TwinLabelTestEnum: string
{
    use Helpers;

    case Hyphen = 'in-progress';
    case Underscore = 'in_progress';
}
