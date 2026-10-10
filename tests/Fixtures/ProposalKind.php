<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures;

use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Helpers;

/**
 * An explicit group that differs from the class name.
 */
#[TranslatedLabels('enums.agent_proposal_kind')]
enum ProposalKind: string
{
    use Helpers;

    case Create = 'create';
    case Edit = 'edit';
}
