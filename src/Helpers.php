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
use RoundlyConsulting\Enums\Support\LabelGroups;

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
     *
     * @throws EnumException when the enum declares no cases
     */
    public static function random(): static
    {
        if (self::count() === 0) {
            throw EnumException::noCases(static::class);
        }

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
     * The plain-array form of toOptions(), for JSON or API output (labels need the translator).
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
     * The cases whose #[TranslatedLabels] key has no line in the given locale (the current
     * one by default), in declaration order — for a test that pins every label translated:
     *
     * ```php
     * expect(OrderStatus::untranslated('sk'))->toBeEmpty();
     * ```
     *
     * The check is strict per locale: a line in fallback_locale does not count. That holds
     * for Laravel's translator; one without hasForLocale() is asked through get(), so its
     * fallback locale counts. A key that resolves to a nested group, or a value holding a
     * ".", is reported, since readable() cannot use it either.
     *
     * @return Collection<int, static>
     *
     * @throws EnumException when the enum has no #[TranslatedLabels] attribute
     */
    public static function untranslated(?string $locale = null): Collection
    {
        $group = LabelGroups::for(static::class) ?? throw EnumException::labelsNotTranslated(static::class);
        $translator = Container::getInstance()->make('translator');
        $locale ??= $translator->getLocale();

        $missing = new Collection;

        foreach (static::cases() as $case) {
            if (! LabelGroups::hasLine($translator, $group, $case->backing(), $locale)) {
                $missing->push($case);
            }
        }

        return $missing;
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
     * When several cases share a label, the first declared case wins.
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
     * When several cases share a label, the first declared case wins.
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
     *
     * @throws EnumException when no CSV form of a value reads back unchanged
     */
    public static function validationRule(): string
    {
        // Inside quotes Laravel's parser (str_getcsv, escape '\') keeps a backslash and
        // the character after it verbatim, so a quote right after a backslash must not be
        // doubled, and a trailing backslash would escape the closing quote. Text after a
        // closing quote is kept up to the next comma, so that trailing run goes there.
        $quote = static fn (string $text): string => '"'.preg_replace_callback(
            '/\\\\.|"/s',
            static fn (array $match): string => $match[0] === '"' ? '""' : $match[0],
            $text,
        ).'"';

        return 'in:'.self::values()
            ->map(static function (string|int $value) use ($quote): string {
                $value = (string) $value;
                $body = rtrim($value, '\\');

                foreach ([$value, $quote($value), $quote($body).substr($value, strlen($body))] as $form) {
                    if (str_getcsv($form, escape: '\\') === [$value]) {
                        return $form;
                    }
                }

                throw EnumException::valueNotRepresentable(static::class, $value);
            })
            ->implode(',');
    }

    /**
     * A human-friendly, translated label derived from the case value or name.
     *
     * This is the one place labels come from: labels(), toOptions(), toArray(), options(),
     * fromLabel(), tryFromLabel() and label() all call it. To customise a
     * label, override readable(), never label() — an override of label() alone leaves
     * every list showing the old label.
     *
     * On an enum marked #[TranslatedLabels], the line "<group>.<value>" (the case name for
     * pure enums) comes first, in the current locale and then fallback_locale. A value
     * holding a "." can never match, because the translator reads the dot as nesting.
     *
     * Otherwise, or when that key has no line, the label is the headline: an int value is
     * its own headline ('0', '-1'); a string value or case name goes through
     * Str::headline() with its '0' parts kept ('level-0' is 'Level 0'). The headline is
     * looked up in the application's translator (JSON or group translations, current
     * locale). When the lookup yields a whole translation group instead of a line —
     * "Auth" names lang/en/auth.php on a case-insensitive filesystem — the untranslated
     * headline is returned.
     *
     * @throws EnumException when #[TranslatedLabels] names an invalid group
     */
    public function readable(): string
    {
        $backing = $this->backing();
        $translator = Container::getInstance()->make('translator');
        $group = LabelGroups::for(static::class);

        if ($group !== null) {
            $line = LabelGroups::line($translator, $group, $backing);

            if ($line !== null) {
                return $line;
            }
        }

        if (is_int($backing)) {
            // An int is its own label: Str::headline() reads '-' as a separator and drops a
            // '0' part, so 0 came out blank and -1 collided with 1.
            $headline = (string) $backing;
        } else {
            // Str::headline() also drops any part that is exactly '0' ('level-0' became
            // 'Level'). A private-use code point stands in for '0' during the call: like a
            // digit it is uncased, no capital and no separator, so the framework splits and
            // title-cases exactly as before and only the lost '0' parts come back. A value
            // already holding that code point keeps the plain headline.
            $zero = "\u{E000}";

            $headline = str_contains($backing, $zero)
                ? Str::headline($backing)
                : str_replace($zero, '0', Str::headline(str_replace('0', $zero, $backing)));
        }

        $translated = $translator->get($headline);

        return is_string($translated) ? $translated : $headline;
    }

    /**
     * An alias of readable() — the term most UI code uses.
     *
     * Override readable(), not this: the lists (labels(), options(), …) read readable().
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
