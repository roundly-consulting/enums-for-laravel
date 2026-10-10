<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * A package-namespaced group, read from Fixtures/lang/{en,sk}/enums.php.
 */
#[TranslatedLabels('fixtures::enums.order_status')]
enum OrderStatus: string
{
    use Helpers;

    case Pending = 'pending';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
