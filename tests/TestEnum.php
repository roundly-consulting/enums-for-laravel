<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests;

use RoundlyConsulting\Enums\Helpers;

enum TestEnum: string
{
    use Helpers;

    case HotNews = 'hot-news';
    case RegularNews = 'regular-news';
    case PrivateNews = 'private-news';
}
