<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Helpers;

/**
 * Overrides readable(), the documented override point.
 */
enum CustomLabel: string
{
    use Helpers;

    case Ok = 'ok';

    public function readable(): string
    {
        return 'Custom '.$this->value;
    }
}
