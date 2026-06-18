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
}
