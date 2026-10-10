<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Support;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;
use ReflectionClass;
use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Exceptions\EnumException;

/**
 * Resolves and reads the #[TranslatedLabels] group of an enum.
 *
 * It lives here rather than in the Helpers trait for two reasons: an enum cannot hold the
 * per-class cache, and every method the trait gains is a name a host enum could already
 * declare and so silently replace.
 *
 * @internal
 */
final class LabelGroups
{
    /**
     * The resolved group per enum class, null when the enum has no attribute. An attribute
     * cannot change at runtime, so the cache is safe across Octane requests.
     *
     * @var array<class-string, string|null>
     */
    private static array $groups = [];

    /**
     * The translation group of an enum, or null when it has no #[TranslatedLabels].
     *
     * @param  class-string  $enum
     *
     * @throws EnumException when the attribute names an invalid group
     */
    public static function for(string $enum): ?string
    {
        if (array_key_exists($enum, self::$groups)) {
            return self::$groups[$enum];
        }

        return self::$groups[$enum] = self::resolve($enum);
    }

    /**
     * The case's grouped line in the current locale or fallback_locale, or null when there
     * is none. A key that resolves to a nested group instead of a line is a miss too.
     */
    public static function line(Translator $translator, string $group, string|int $backing): ?string
    {
        $key = self::key($group, $backing);
        $line = $translator->get($key);

        return is_string($line) && $line !== $key ? $line : null;
    }

    /**
     * Whether the case's grouped key has a line in exactly this locale.
     *
     * Laravel's translator answers per locale through hasForLocale(). Any other translator
     * only offers get(), which also tries the fallback locale, so a line there counts.
     */
    public static function hasLine(Translator $translator, string $group, string|int $backing, string $locale): bool
    {
        $key = self::key($group, $backing);

        if (method_exists($translator, 'hasForLocale') && ! $translator->hasForLocale($key, $locale)) {
            return false;
        }

        $line = $translator->get($key, [], $locale);

        return is_string($line) && $line !== $key;
    }

    private static function key(string $group, string|int $backing): string
    {
        return $group.'.'.$backing;
    }

    /**
     * @param  class-string  $enum
     */
    private static function resolve(string $enum): ?string
    {
        $reflection = new ReflectionClass($enum);
        $attribute = $reflection->getAttributes(TranslatedLabels::class)[0] ?? null;

        if ($attribute === null) {
            return null;
        }

        $group = $attribute->newInstance()->group;

        if ($group === null) {
            return 'enums.'.Str::snake($reflection->getShortName());
        }

        // A group that is blank or starts or ends with whitespace, "." or ":" can only ever
        // miss, so every label would quietly fall back to its headline. Fail instead.
        if ($group === '' || trim($group, " \t\n\r\0\x0B.:") !== $group) {
            throw EnumException::invalidLabelGroup($enum, $group);
        }

        return $group;
    }
}
