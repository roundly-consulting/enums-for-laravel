<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Tests\Fixtures\Bc;

use BackedEnum;
use Closure;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use RoundlyConsulting\Enums\DataTransferObjects\EnumOption;
use RoundlyConsulting\Enums\Tests\AreaTestEnum;
use RoundlyConsulting\Enums\Tests\BackslashTestEnum;
use RoundlyConsulting\Enums\Tests\EmptyTestEnum;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PrivateUseTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\SeparatorTestEnum;
use RoundlyConsulting\Enums\Tests\SignedIntTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;
use RoundlyConsulting\Enums\Tests\TwinLabelTestEnum;
use RoundlyConsulting\Enums\Tests\ZeroPartTestEnum;
use Throwable;
use UnitEnum;

/**
 * Every output of the 1.0.1 public API, for every fixture enum that has no
 * #[TranslatedLabels] attribute, with and without translations loaded.
 *
 * golden-1.0.1.php was written by this class running against the released 1.0.1
 * source (ff7d52a). An enum without the attribute must reproduce it exactly, so
 * the translated-label work can never leak into enums that did not opt in. The
 * "translated" scenario also loads a grouped enums.<snake>.<value> decoy line for
 * every case: none of them may show up.
 */
final class Snapshot
{
    /** @var list<class-string<UnitEnum>> */
    public const array ENUMS = [
        TestEnum::class,
        IntTestEnum::class,
        PureTestEnum::class,
        SignedIntTestEnum::class,
        AreaTestEnum::class,
        ZeroPartTestEnum::class,
        PrivateUseTestEnum::class,
        TwinLabelTestEnum::class,
        SeparatorTestEnum::class,
        BackslashTestEnum::class,
        EmptyTestEnum::class,
    ];

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public static function take(): array
    {
        app()->setLocale('en');

        $snapshot = ['plain' => ['en' => self::all()]];

        self::loadTranslations();

        foreach (['en', 'sk'] as $locale) {
            app()->setLocale($locale);
            $snapshot['translated'][$locale] = self::all();
        }

        app()->setLocale('en');

        return $snapshot;
    }

    /**
     * @return array<string, mixed>
     */
    private static function all(): array
    {
        $out = [];

        foreach (self::ENUMS as $enum) {
            $out[$enum] = self::of($enum);
        }

        return $out;
    }

    /**
     * @param  class-string<UnitEnum>  $enum
     * @return array<string, mixed>
     */
    private static function of(string $enum): array
    {
        $cases = [];

        foreach ($enum::cases() as $case) {
            $cases[$case->name] = [
                'readable' => $case->readable(),
                'label' => $case->label(),
                'fromLabel' => self::capture(fn () => $enum::fromLabel($case->label())->name),
                'tryFromLabel' => $enum::tryFromLabel($case->readable())?->name,
                'fromName' => $enum::fromName($case->name)->name,
                'tryFromName' => $enum::tryFromName($case->name)?->name,
                'hasName' => $enum::hasName($case->name),
                'hasValue' => $enum::hasValue(self::backing($case)),
                'is' => $case->is($case),
                'isIn' => $case->isIn($enum::cases()),
            ];
        }

        return [
            'storable' => $enum::storable()->all(),
            'values' => $enum::values()->all(),
            'names' => $enum::names()->all(),
            'labels' => $enum::labels()->all(),
            'count' => $enum::count(),
            'collect' => $enum::collect()->map(fn (UnitEnum $case): string => $case->name)->all(),
            'toOptions' => $enum::toOptions()->all(),
            'toArray' => $enum::toArray(),
            'options' => $enum::options()->map(fn (EnumOption $option): array => $option->toArray())->all(),
            'optionClasses' => $enum::options()->map(fn (object $option): string => $option::class)->all(),
            'validationRule' => self::capture(fn () => $enum::validationRule()),
            'cases' => $cases,
            'misses' => [
                'fromName' => self::capture(fn () => $enum::fromName('Nope')),
                'fromLabel' => self::capture(fn () => $enum::fromLabel('Nope')),
                'tryFromName' => $enum::tryFromName(null),
                'tryFromLabel' => $enum::tryFromLabel(null),
                'tryFromLabelMiss' => $enum::tryFromLabel('Nope'),
                'hasName' => $enum::hasName('Nope'),
                'hasValue' => $enum::hasValue('Nope'),
                'random' => $enum::count() === 0 ? self::capture(fn () => $enum::random()) : null,
            ],
        ];
    }

    private static function loadTranslations(): void
    {
        foreach (['en', 'sk'] as $locale) {
            $decoys = [];

            foreach (self::ENUMS as $enum) {
                $group = Str::snake(class_basename($enum));

                foreach ($enum::cases() as $case) {
                    $decoys['enums.'.$group.'.'.self::backing($case)] = "DECOY {$locale}";
                }
            }

            Lang::addLines($decoys, $locale);
        }

        Lang::addLines(['*.Hot News' => 'Breaking', '*.5' => 'Five'], 'en', '*');
        Lang::addLines([
            '*.Hot News' => 'Horúce správy',
            '*.5' => 'Päť',
            '*.Active' => 'Aktívny',
            '*.Level 0' => 'Úroveň 0',
            '*.In Progress' => 'Prebieha',
            '*.-1' => 'Mínus jedna',
        ], 'sk', '*');
        Lang::addLines(['Auth.failed' => 'These credentials do not match our records.'], 'en');
        Lang::addLines(['Auth.failed' => 'Prihlasovacie údaje sa nezhodujú.'], 'sk');
    }

    private static function backing(UnitEnum $case): string|int
    {
        return $case instanceof BackedEnum ? $case->value : $case->name;
    }

    private static function capture(Closure $call): mixed
    {
        try {
            return $call();
        } catch (Throwable $e) {
            return ['exception' => $e::class, 'message' => $e->getMessage()];
        }
    }
}
