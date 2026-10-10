<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;

/**
 * The attribute without the Helpers trait — and with an invalid group, which nothing reads.
 */
#[TranslatedLabels('')]
enum InertAttribute: string
{
    case Draft = 'draft';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
