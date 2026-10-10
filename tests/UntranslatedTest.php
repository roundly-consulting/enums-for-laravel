<?php

declare(strict_types=1);

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Lang;
use RoundlyConsulting\Enums\Exceptions\EnumException;
use RoundlyConsulting\Enums\Tests\Fixtures\OrderStatus;
use RoundlyConsulting\Enums\Tests\Fixtures\Priority;
use RoundlyConsulting\Enums\Tests\Fixtures\Release;
use RoundlyConsulting\Enums\Tests\Fixtures\ToolAuth;
use RoundlyConsulting\Enums\Tests\Fixtures\Visibility;
use RoundlyConsulting\Enums\Tests\TestEnum;

beforeEach(function () {
    app('translator')->addNamespace('fixtures', __DIR__.'/Fixtures/lang');
});

it('lists the cases with no grouped line in the given locale', function () {
    Lang::addLines([
        'enums.tool_auth.none' => 'No authentication',
        'enums.tool_auth.api_key' => 'API key',
        'enums.tool_auth.oauth' => 'OAuth',
    ], 'en');
    Lang::addLines(['enums.tool_auth.none' => 'Bez overenia'], 'sk');

    expect(ToolAuth::untranslated('en'))->toBeCollection()->toBeEmpty()
        ->and(ToolAuth::untranslated('sk')->all())->toBe([ToolAuth::ApiKey, ToolAuth::OAuth]);
});

it('checks one locale strictly, never counting the fallback locale', function () {
    expect(OrderStatus::untranslated('en'))->toBeEmpty()
        ->and(OrderStatus::untranslated('sk')->all())->toBe([OrderStatus::Cancelled])
        ->and(OrderStatus::untranslated('de')->all())->toBe(OrderStatus::cases());
});

it('checks the current locale when none is given', function () {
    app()->setLocale('sk');

    expect(OrderStatus::untranslated()->all())->toBe([OrderStatus::Cancelled]);
});

it('returns a zero-based list', function () {
    expect(OrderStatus::untranslated('sk')->keys()->all())->toBe([0]);
});

it('reports int, negative and pure-enum keys the same way', function () {
    Lang::addLines(['enums.priority.-1' => 'Overdue', 'enums.priority.5' => 'Normal'], 'en');
    Lang::addLines(['enums.visibility.Private' => 'Only me'], 'en');

    expect(Priority::untranslated('en')->all())->toBe([Priority::Low])
        ->and(Visibility::untranslated('en')->all())->toBe([Visibility::Public]);
});

it('reports a value holding a dot, which readable() cannot translate', function () {
    expect(Release::untranslated('en')->all())->toBe([Release::Stable]);
});

it('reports a key that resolves to a nested group', function () {
    Lang::addLines([
        'enums.tool_auth.none.short' => 'None',
        'enums.tool_auth.api_key' => 'API key',
        'enums.tool_auth.oauth' => 'OAuth',
    ], 'en');

    expect(ToolAuth::untranslated('en')->all())->toBe([ToolAuth::None]);
});

it('refuses an enum without the attribute', function () {
    expect(fn () => TestEnum::untranslated('en'))->toThrow(
        EnumException::class,
        'Enum ['.TestEnum::class.'] has no #[TranslatedLabels] attribute, so it has no translation keys to check.',
    );
});

it('falls back to get() on a translator without hasForLocale(), counting the fallback locale', function () {
    app()->instance('translator', new class implements Translator
    {
        /** @var array<string, array<string, mixed>> */
        private array $lines = [
            'en' => ['enums.tool_auth.none' => 'No authentication', 'enums.tool_auth.api_key' => 'API key', 'enums.tool_auth.oauth' => ['nested' => 'x']],
            'sk' => ['enums.tool_auth.none' => 'Bez overenia'],
        ];

        private string $locale = 'sk';

        public function get($key, array $replace = [], $locale = null)
        {
            return $this->lines[$locale ?? $this->locale][$key] ?? $this->lines['en'][$key] ?? $key;
        }

        public function choice($key, $number, array $replace = [], $locale = null)
        {
            return $this->get($key, $replace, $locale);
        }

        public function getLocale()
        {
            return $this->locale;
        }

        public function setLocale($locale)
        {
            $this->locale = $locale;
        }
    });

    expect(ToolAuth::untranslated()->all())->toBe([ToolAuth::OAuth])
        ->and(ToolAuth::untranslated('de')->all())->toBe([ToolAuth::OAuth])
        ->and(ToolAuth::labels()->all())->toBe(['Bez overenia', 'API key', 'Oauth']);
});
