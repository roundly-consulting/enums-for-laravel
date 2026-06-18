<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\DataTransferObjects;

/**
 * A select-friendly representation of a single enum case.
 *
 * For backed enums `$value` is the backing value; for pure enums it falls back
 * to the case `name`.
 */
final readonly class EnumOption
{
    public function __construct(
        public string|int $value,
        public string $label,
        public string $name,
    ) {}

    /**
     * @return array{value: string|int, label: string, name: string}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'name' => $this->name,
        ];
    }
}
