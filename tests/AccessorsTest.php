<?php

declare(strict_types=1);

use RoundlyConsulting\Enums\Exceptions\EnumException;
use RoundlyConsulting\Enums\Tests\EmptyTestEnum;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;

it('returns names in declaration order', function () {
    expect(TestEnum::names())
        ->toBeCollection()
        ->toArray()->toBe(['HotNews', 'RegularNews', 'PrivateNews']);
});

it('returns values as a sibling of storable', function () {
    expect(TestEnum::values()->toArray())->toBe(TestEnum::storable()->toArray())
        ->and(TestEnum::values()->toArray())->toBe(['hot-news', 'regular-news', 'private-news']);
});

it('returns int-backed values', function () {
    expect(IntTestEnum::values())
        ->toBeCollection()
        ->toArray()->toBe([0, 5, 10]);
});

it('returns case names as values and storable for pure enums', function () {
    expect(PureTestEnum::values()->all())->toBe(['Active', 'Archived'])
        ->and(PureTestEnum::storable()->all())->toBe(['Active', 'Archived']);
});

it('returns labels for each case', function () {
    expect(TestEnum::labels())
        ->toBeCollection()
        ->toArray()->toBe(['Hot News', 'Regular News', 'Private News']);
});

it('returns labels for pure enums using the name', function () {
    expect(PureTestEnum::labels()->toArray())->toBe(['Active', 'Archived']);
});

it('collects cases as enum instances', function () {
    expect(TestEnum::collect())
        ->toBeCollection()
        ->toArray()->toBe(TestEnum::cases());
});

it('counts the cases', function () {
    expect(TestEnum::count())->toBe(3)
        ->and(PureTestEnum::count())->toBe(2);
});

it('returns a random case that is always a real member', function () {
    foreach (range(1, 20) as $ignored) {
        expect(TestEnum::cases())->toContain(TestEnum::random());
    }
});

it('throws an enum exception when picking a random case of an enum without cases', function () {
    expect(fn () => EmptyTestEnum::random())
        ->toThrow(EnumException::class, 'Enum ['.EmptyTestEnum::class.'] has no cases.');
});
