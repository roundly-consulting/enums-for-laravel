<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

/**
 * "auth" headlines to "Auth" — the name of a translation group once lang/en/auth.php is
 * loaded, which a case-insensitive filesystem does for the key "Auth".
 */
enum AreaTestEnum: string
{
    use Helpers;

    case Auth = 'auth';
    case Billing = 'billing';
}
