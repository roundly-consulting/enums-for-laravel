<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * A display-ready representation of a single enum case: its value, translated label and
 * colour, for badges and status chips.
 *
 * For backed enums `$value` is the backing value with its native type (an int stays an int);
 * for pure enums it falls back to the case name. `$color` is null when the enum does not
 * implement HasColor.
 *
 * @implements Arrayable<string, string|int|null>
 */
final readonly class EnumPresentation implements Arrayable, JsonSerializable
{
    public function __construct(
        public string|int $value,
        public string $label,
        public ?string $color,
    ) {}

    /**
     * @return array{value: string|int, label: string, color: string|null}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'color' => $this->color,
        ];
    }

    /**
     * @return array{value: string|int, label: string, color: string|null}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
