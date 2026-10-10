<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Lang;
use RoundlyConsulting\Enums\Attributes\TranslatedLabels;
use RoundlyConsulting\Enums\Exceptions\EnumException;
use RoundlyConsulting\Enums\Helpers;
use RoundlyConsulting\Enums\Tests\Fixtures\AccessArea;
use RoundlyConsulting\Enums\Tests\Fixtures\HTTPVerb;
use RoundlyConsulting\Enums\Tests\Fixtures\InertAttribute;
use RoundlyConsulting\Enums\Tests\Fixtures\OrderStatus;
use RoundlyConsulting\Enums\Tests\Fixtures\Priority;
use RoundlyConsulting\Enums\Tests\Fixtures\ProposalKind;
use RoundlyConsulting\Enums\Tests\Fixtures\Release;
use RoundlyConsulting\Enums\Tests\Fixtures\ToolAuth;
use RoundlyConsulting\Enums\Tests\Fixtures\Visibility;
use RoundlyConsulting\Enums\Tests\TestEnum;

#[TranslatedLabels('')]
enum BlankLabelGroupEnum: string
{
    use Helpers;

    case A = 'a';
}

#[TranslatedLabels(' ')]
enum WhitespaceLabelGroupEnum: string
{
    use Helpers;

    case A = 'a';
}

#[TranslatedLabels('enums.')]
enum TrailingDotLabelGroupEnum: string
{
    use Helpers;

    case A = 'a';
}

#[TranslatedLabels('.x')]
enum LeadingDotLabelGroupEnum: string
{
    use Helpers;

    case A = 'a';
}

#[TranslatedLabels('fixtures::')]
enum DanglingNamespaceLabelGroupEnum: string
{
    use Helpers;

    case A = 'a';
}

#[TranslatedLabels(' enums.padded')]
enum PaddedLabelGroupEnum: string
{
    use Helpers;

    case A = 'a';
}

beforeEach(function () {
    app('translator')->addNamespace('fixtures', __DIR__.'/Fixtures/lang');

    Lang::addLines([
        'enums.tool_auth.none' => 'No authentication',
        'enums.tool_auth.api_key' => 'API key',
    ], 'en');
    Lang::addLines([
        'enums.tool_auth.none' => 'Bez overenia',
        'enums.tool_auth.api_key' => 'API kľúč',
    ], 'sk');
});

it('reads the bare attribute from enums.<snake_case class>.<value>', function () {
    expect(ToolAuth::ApiKey->readable())->toBe('API key')
        ->and(ToolAuth::None->readable())->toBe('No authentication');
});

it('translates the bare attribute in the current locale', function () {
    app()->setLocale('sk');

    expect(ToolAuth::ApiKey->readable())->toBe('API kľúč')
        ->and(ToolAuth::None->label())->toBe('Bez overenia');
});

it('reads an explicit group', function () {
    Lang::addLines(['enums.agent_proposal_kind.edit' => 'Edit proposal'], 'en');
    Lang::addLines(['enums.agent_proposal_kind.edit' => 'Návrh úpravy'], 'sk');

    expect(ProposalKind::Edit->readable())->toBe('Edit proposal')
        ->and(ProposalKind::Create->readable())->toBe('Create');

    app()->setLocale('sk');

    expect(ProposalKind::Edit->readable())->toBe('Návrh úpravy');
});

it('reads a package-namespaced group from its translation files', function () {
    expect(OrderStatus::Pending->readable())->toBe('Awaiting payment');

    app()->setLocale('sk');

    expect(OrderStatus::Pending->readable())->toBe('Čaká na platbu')
        ->and(OrderStatus::Shipped->readable())->toBe('Na ceste');
});

it('translates every list built on readable()', function () {
    app()->setLocale('sk');

    expect(ToolAuth::labels()->all())->toBe(['Bez overenia', 'API kľúč', 'Oauth'])
        ->and(ToolAuth::toOptions()->all())->toBe(['none' => 'Bez overenia', 'api_key' => 'API kľúč', 'oauth' => 'Oauth'])
        ->and(ToolAuth::toArray())->toBe(['none' => 'Bez overenia', 'api_key' => 'API kľúč', 'oauth' => 'Oauth'])
        ->and(ToolAuth::options()->map->toArray()->all())->toBe([
            ['value' => 'none', 'label' => 'Bez overenia', 'name' => 'None'],
            ['value' => 'api_key', 'label' => 'API kľúč', 'name' => 'ApiKey'],
            ['value' => 'oauth', 'label' => 'Oauth', 'name' => 'OAuth'],
        ])
        ->and(ToolAuth::fromLabel('API kľúč'))->toBe(ToolAuth::ApiKey)
        ->and(ToolAuth::tryFromLabel('Bez overenia'))->toBe(ToolAuth::None)
        ->and(ToolAuth::tryFromLabel('API key'))->toBeNull()
        ->and(ToolAuth::ApiKey->label())->toBe(ToolAuth::ApiKey->readable());
});

it('falls back to fallback_locale when the current locale has no line', function () {
    app()->setLocale('sk');

    expect(OrderStatus::Cancelled->readable())->toBe('Cancelled by customer');
});

it('falls back to the JSON headline line when no locale has a grouped line', function () {
    Lang::addLines(['*.Oauth' => 'OAuth 2.0'], 'en', '*');

    expect(ToolAuth::OAuth->readable())->toBe('OAuth 2.0');
});

it('falls back to the raw headline when nothing translates', function () {
    expect(ToolAuth::OAuth->readable())->toBe('Oauth')
        ->and(Priority::Normal->readable())->toBe('5');
});

it('treats a key that resolves to a nested group as a miss', function () {
    Lang::addLines(['enums.tool_auth.oauth.short' => 'OAuth'], 'en');

    expect(ToolAuth::OAuth->readable())->toBe('Oauth');
});

it('keeps the translation-group collision guard behind a grouped miss', function () {
    Lang::addLines(['Auth.failed' => 'These credentials do not match our records.'], 'en');
    Lang::addLines(['enums.access_area.billing' => 'Invoices'], 'en');

    expect(AccessArea::Auth->readable())->toBe('Auth')
        ->and(AccessArea::labels()->all())->toBe(['Auth', 'Invoices']);
});

it('keys an int-backed enum by its value, keeping a negative sign', function () {
    Lang::addLines([
        'enums.priority.5' => 'Normal',
        'enums.priority.-1' => 'Overdue',
    ], 'en');

    expect(Priority::Normal->readable())->toBe('Normal')
        ->and(Priority::Debt->readable())->toBe('Overdue')
        ->and(Priority::Low->readable())->toBe('1')
        ->and(Priority::fromLabel('Overdue'))->toBe(Priority::Debt);
});

it('keys a pure enum by its case name', function () {
    Lang::addLines(['enums.visibility.Public' => 'Everyone'], 'en');

    expect(Visibility::Public->readable())->toBe('Everyone')
        ->and(Visibility::Private->readable())->toBe('Private');
});

it('misses on a value holding a dot and falls back to the headline', function () {
    expect(Release::Stable->readable())->toBe('V1.0')
        ->and(Release::Next->readable())->toBe('Version two');
});

it('derives the default group from the class name as written, acronyms included', function () {
    Lang::addLines(['enums.h_t_t_p_verb.get' => 'GET'], 'en');

    expect(HTTPVerb::Get->readable())->toBe('GET');
});

it('rejects an invalid explicit group loudly', function (string $enum, string $group) {
    expect(fn () => $enum::A->readable())->toThrow(
        EnumException::class,
        "Enum [{$enum}] declares an invalid #[TranslatedLabels] group [{$group}]",
    );
})->with([
    'blank' => [BlankLabelGroupEnum::class, ''],
    'whitespace' => [WhitespaceLabelGroupEnum::class, ' '],
    'trailing dot' => [TrailingDotLabelGroupEnum::class, 'enums.'],
    'leading dot' => [LeadingDotLabelGroupEnum::class, '.x'],
    'dangling namespace' => [DanglingNamespaceLabelGroupEnum::class, 'fixtures::'],
    'padded' => [PaddedLabelGroupEnum::class, ' enums.padded'],
]);

it('rejects an invalid group on every use, not only the first', function () {
    expect(fn () => BlankLabelGroupEnum::labels())->toThrow(EnumException::class)
        ->and(fn () => BlankLabelGroupEnum::A->label())->toThrow(EnumException::class);
});

it('leaves the attribute inert on an enum without the Helpers trait', function () {
    expect(InertAttribute::Draft->label())->toBe('Draft')
        ->and(InertAttribute::from('draft'))->toBe(InertAttribute::Draft);
});

it('ignores grouped lines on an enum without the attribute', function () {
    Lang::addLines(['enums.test_enum.hot-news' => 'Grouped'], 'en');

    expect(TestEnum::HotNews->readable())->toBe('Hot News')
        ->and(TestEnum::labels()->first())->toBe('Hot News');
});
