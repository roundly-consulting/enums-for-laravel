<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums;

use BackedEnum;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RoundlyConsulting\Enums\DataTransferObjects\EnumOption;
use RoundlyConsulting\Enums\Exceptions\EnumException;

/**
 * Convenience helpers for PHP enums.
 *
 * Designed for backed enums, but every method also works on pure (non-backed)
 * enums: wherever a backed value would be read, the case name is used instead.
 *
 * ```php
 * enum Status: string
 * {
 *     use Helpers;
 * }
 * ```
 */
trait Helpers
{
    /**
     * The raw backed values of every case (case names for pure enums), in declaration order.
     *
     * @return Collection<int, string|int>
     */
    public static function storable(): Collection
    {
        return (new Collection(static::cases()))
            ->map(static fn (self $enum): string|int => $enum->backing())
            ->values();
    }

    /**
     * The backed values of every case (case names for pure enums) — the conventional sibling of storable().
     *
     * @return Collection<int, string|int>
     */
    public static function values(): Collection
    {
        return self::storable();
    }

    /**
     * The case names of every case, in declaration order.
     *
     * @return Collection<int, string>
     */
    public static function names(): Collection
    {
        return (new Collection(static::cases()))->pluck('name')->values();
    }

    /**
     * The readable labels of every case, in declaration order.
     *
     * @return Collection<int, string>
     */
    public static function labels(): Collection
    {
        return (new Collection(static::cases()))
            ->map(static fn (self $enum): string => $enum->readable())
            ->values();
    }

    /**
     * Every case as a collection of enum instances.
     *
     * @return Collection<int, self>
     */
    public static function collect(): Collection
    {
        $cases = new Collection;

        foreach (static::cases() as $case) {
            $cases->push($case);
        }

        return $cases;
    }

    /**
     * The number of cases declared on the enum.
     */
    public static function count(): int
    {
        return count(static::cases());
    }

    /**
     * A random case.
     */
    public static function random(): static
    {
        $cases = static::cases();

        return $cases[array_rand($cases)];
    }

    /**
     * A value => readable-label map for every case, suitable for select inputs.
     *
     * @return Collection<array-key, string>
     */
    public static function toOptions(): Collection
    {
        return (new Collection(static::cases()))->mapWithKeys(static fn (self $enum): array => [
            $enum->backing() => $enum->readable(),
        ]);
    }

    /**
     * The plain-array form of toOptions(), for config or JSON output.
     *
     * @return array<array-key, string>
     */
    public static function toArray(): array
    {
        return self::toOptions()->all();
    }

    /**
     * A list of {value, label, name} option DTOs, ready for JS/Inertia selects.
     *
     * @return Collection<int, EnumOption>
     */
    public static function options(): Collection
    {
        return (new Collection(static::cases()))
            ->map(static fn (self $enum): EnumOption => new EnumOption(
                value: $enum->backing(),
                label: $enum->readable(),
                name: $enum->name,
            ))
            ->values();
    }

    /**
     * Resolve a case by its name, throwing when none matches.
     *
     * @throws EnumException
     */
    public static function fromName(string $name): static
    {
        return static::tryFromName($name)
            ?? throw EnumException::nameNotFound(static::class, $name);
    }

    /**
     * Resolve a case by its name, or null when none matches.
     */
    public static function tryFromName(?string $name): ?static
    {
        if ($name === null) {
            return null;
        }

        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Resolve a case by its readable label, throwing when none matches.
     *
     * @throws EnumException
     */
    public static function fromLabel(string $label): static
    {
        return static::tryFromLabel($label)
            ?? throw EnumException::labelNotFound(static::class, $label);
    }

    /**
     * Resolve a case by its readable label, or null when none matches.
     */
    public static function tryFromLabel(?string $label): ?static
    {
        if ($label === null) {
            return null;
        }

        foreach (static::cases() as $case) {
            if ($case->readable() === $label) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Whether a case with the given name exists.
     */
    public static function hasName(string $name): bool
    {
        return static::tryFromName($name) !== null;
    }

    /**
     * Whether a case with the given backed value (case name for pure enums) exists.
     * The comparison is strict: '5' does not match an int-backed 5.
     */
    public static function hasValue(string|int $value): bool
    {
        foreach (static::cases() as $case) {
            if ($case->backing() === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * A Laravel "in:..." validation rule string built from values().
     *
     * Laravel reads the list back as CSV, so a value that would not survive that
     * (an empty one, one containing a comma, or one starting with a double quote)
     * is quoted with its inner quotes doubled; every other value stays bare, as in
     * 'in:draft,"a,b",final'. Pass it as an array element, not inside a
     * pipe-delimited rule string, when a value contains "|".
     */
    public static function validationRule(): string
    {
        return 'in:'.self::values()
            ->map(static function (string|int $value): string {
                $value = (string) $value;

                return str_getcsv($value, escape: '\\') === [$value]
                    ? $value
                    : '"'.str_replace('"', '""', $value).'"';
            })
            ->implode(',');
    }

    /**
     * A human-friendly, translated label derived from the case value or name.
     *
     * The headline is looked up in the application's translator (JSON or group
     * translations, current locale). When the lookup yields a whole translation
     * group instead of a line — "Auth" names lang/en/auth.php on a case-insensitive
     * filesystem — the untranslated headline is returned.
     */
    public function readable(): string
    {
        $headline = Str::headline((string) $this->backing());
        $translated = Container::getInstance()->make('translator')->get($headline);

        return is_string($translated) ? $translated : $headline;
    }

    /**
     * An alias of readable() — the term most UI code uses.
     */
    public function label(): string
    {
        return $this->readable();
    }

    public function is(self $enum): bool
    {
        return $this === $enum;
    }

    public function isNot(self $enum): bool
    {
        return ! $this->is($enum);
    }

    /**
     * @param  array<int, self>  $enums
     */
    public function isIn(array $enums): bool
    {
        foreach ($enums as $enum) {
            if ($this === $enum) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, self>  $enums
     */
    public function isNotIn(array $enums): bool
    {
        return ! $this->isIn($enums);
    }

    /**
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIs(self $enum, Closure $callback, ?Closure $default = null): self
    {
        if ($this->is($enum)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIsNot(self $enum, Closure $callback, ?Closure $default = null): self
    {
        if ($this->isNot($enum)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * @param  array<int, self>  $enums
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIsIn(array $enums, Closure $callback, ?Closure $default = null): self
    {
        if ($this->isIn($enums)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * @param  array<int, self>  $enums
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIsNotIn(array $enums, Closure $callback, ?Closure $default = null): self
    {
        if ($this->isNotIn($enums)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * The backing value for the case — the backed value, or the name for pure enums.
     */
    private function backing(): string|int
    {
        return $this instanceof BackedEnum ? $this->value : $this->name;
    }
}
