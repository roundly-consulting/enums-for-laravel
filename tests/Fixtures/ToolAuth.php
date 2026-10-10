<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * The bare attribute: keys are enums.tool_auth.<value>.
 */
#[TranslatedLabels]
enum ToolAuth: string
{
    use Helpers;

    case None = 'none';
    case ApiKey = 'api_key';
    case OAuth = 'oauth';
}
