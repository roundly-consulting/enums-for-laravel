<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Helpers;

/**
 * A color() of its own that does not implement HasColor: still legal, never read.
 */
enum LegacyColor: string
{
    use Helpers;

    case Red = 'red';

    /**
     * @return list<int>
     */
    public function color(): array
    {
        return [255, 0, 0];
    }
}
