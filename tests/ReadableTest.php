<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Lang;
use RoundlyConsulting\Enums\Tests\AreaTestEnum;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;

it('returns a readable label for backed enums', function () {
    expect(TestEnum::HotNews->readable())->toBe('Hot News')
        ->and(IntTestEnum::Medium->readable())->toBe('5');
});

it('returns a readable label for pure enums using the name', function () {
    expect(PureTestEnum::Active->readable())->toBe('Active');
});

it('exposes label() as an alias of readable()', function () {
    expect(TestEnum::HotNews->label())->toBe(TestEnum::HotNews->readable())
        ->and(PureTestEnum::Active->label())->toBe('Active');
});

it('honours translation overrides for labels', function () {
    Lang::addLines(['*.Hot News' => 'Breaking'], 'en', '*');

    expect(TestEnum::HotNews->readable())->toBe('Breaking');
});

it('falls back to the headline when it names a translation group', function () {
    Lang::addLines(['Auth.failed' => 'These credentials do not match our records.'], 'en');

    expect(AreaTestEnum::Auth->readable())->toBe('Auth')
        ->and(AreaTestEnum::labels()->all())->toBe(['Auth', 'Billing'])
        ->and(AreaTestEnum::toArray())->toBe(['auth' => 'Auth', 'billing' => 'Billing'])
        ->and(AreaTestEnum::fromLabel('Auth'))->toBe(AreaTestEnum::Auth);
});

it('translates labels through the container translator in the current locale', function () {
    Lang::addLines(['*.Hot News' => 'Horúce správy'], 'sk', '*');
    app()->setLocale('sk');

    expect(TestEnum::HotNews->readable())->toBe('Horúce správy')
        ->and(TestEnum::RegularNews->readable())->toBe('Regular News');
});
