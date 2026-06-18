<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Lang;
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
