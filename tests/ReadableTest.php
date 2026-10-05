<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Lang;
use RoundlyConsulting\Enums\Tests\AreaTestEnum;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PrivateUseTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\SignedIntTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;
use RoundlyConsulting\Enums\Tests\ZeroPartTestEnum;

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

it('labels an int zero as "0"', function () {
    expect(IntTestEnum::Low->readable())->toBe('0')
        ->and(IntTestEnum::toArray())->toBe([0 => '0', 5 => '5', 10 => '10'])
        ->and(IntTestEnum::fromLabel('0'))->toBe(IntTestEnum::Low);
});

it('keeps the sign of a negative int label', function () {
    expect(SignedIntTestEnum::labels()->all())->toBe(['-1', '1'])
        ->and(SignedIntTestEnum::fromLabel('1'))->toBe(SignedIntTestEnum::Plus)
        ->and(SignedIntTestEnum::fromLabel('-1'))->toBe(SignedIntTestEnum::Minus);
});

it('keeps "0" parts of string values in the label', function () {
    expect(ZeroPartTestEnum::labels()->all())
        ->toBe(['Level', 'Level 0', 'Level 10', 'V1 0 0', 'Room 0', '0 Floor'])
        ->and(ZeroPartTestEnum::fromLabel('Level 0'))->toBe(ZeroPartTestEnum::LevelZero);
});

it('leaves a value already holding the zero mask to the plain headline', function () {
    expect(PrivateUseTestEnum::Masked->readable())->toBe("\u{E000} 1");
});
