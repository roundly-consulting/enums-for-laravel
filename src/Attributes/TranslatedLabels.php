<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Attributes;

use Attribute;

/**
 * Labels an enum that uses the Helpers trait from a translation group instead of its headline.
 *
 * Each case reads the line "<group>.<value>" (the case name for pure enums) in the current
 * locale, then fallback_locale; a case with no line keeps the 1.0 headline label. Without an
 * argument the group is "enums." plus the snake_case class name:
 *
 * ```php
 * #[TranslatedLabels]                                // enums.order_status.<value>
 * #[TranslatedLabels('enums.checkout_status')]       // an explicit group
 * #[TranslatedLabels('billing::enums.order_status')] // a package's own translation files
 * ```
 *
 * An acronym class name snakes letter by letter (HTTPMethod is h_t_t_p_method), so pass
 * such a group explicitly.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class TranslatedLabels
{
    public function __construct(
        public ?string $group = null,
    ) {}
}
