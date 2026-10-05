<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Exceptions;

use InvalidArgumentException;

class EnumException extends InvalidArgumentException
{
    public static function nameNotFound(string $enum, string $name): self
    {
        return new self("No case named [{$name}] exists on enum [{$enum}].");
    }

    public static function labelNotFound(string $enum, string $label): self
    {
        return new self("No case with label [{$label}] exists on enum [{$enum}].");
    }

    public static function noCases(string $enum): self
    {
        return new self("Enum [{$enum}] has no cases.");
    }

    public static function valueNotRepresentable(string $enum, string $value): self
    {
        return new self("Value [{$value}] of enum [{$enum}] cannot be written into an in: rule that reads back unchanged; validate it with Rule::enum() instead.");
    }
}
