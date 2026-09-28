<?php

declare(strict_types=1);

use RoundlyConsulting\Enums\Exceptions\EnumException;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;

it('resolves a case by name', function () {
    expect(TestEnum::fromName('HotNews'))->toBe(TestEnum::HotNews)
        ->and(PureTestEnum::fromName('Active'))->toBe(PureTestEnum::Active);
});

it('throws when a name is not found', function () {
    TestEnum::fromName('Missing');
})->throws(EnumException::class);

it('tries to resolve a case by name', function () {
    expect(TestEnum::tryFromName('RegularNews'))->toBe(TestEnum::RegularNews)
        ->and(TestEnum::tryFromName('Missing'))->toBeNull()
        ->and(TestEnum::tryFromName(null))->toBeNull();
});

it('resolves a case by label', function () {
    expect(TestEnum::fromLabel('Hot News'))->toBe(TestEnum::HotNews)
        ->and(PureTestEnum::fromLabel('Active'))->toBe(PureTestEnum::Active);
});

it('throws when a label is not found', function () {
    TestEnum::fromLabel('Nope');
})->throws(EnumException::class);

it('tries to resolve a case by label', function () {
    expect(TestEnum::tryFromLabel('Regular News'))->toBe(TestEnum::RegularNews)
        ->and(TestEnum::tryFromLabel('Nope'))->toBeNull()
        ->and(TestEnum::tryFromLabel(null))->toBeNull();
});

it('reports whether a name exists', function () {
    expect(TestEnum::hasName('HotNews'))->toBeTrue()
        ->and(TestEnum::hasName('Missing'))->toBeFalse();
});

it('reports whether a backed value exists', function () {
    expect(TestEnum::hasValue('hot-news'))->toBeTrue()
        ->and(TestEnum::hasValue('missing'))->toBeFalse()
        ->and(IntTestEnum::hasValue(0))->toBeTrue()
        ->and(IntTestEnum::hasValue(99))->toBeFalse();
});

it('matches the case name for hasValue on pure enums', function () {
    expect(PureTestEnum::hasValue('Active'))->toBeTrue()
        ->and(PureTestEnum::hasValue('Deleted'))->toBeFalse();
});

it('compares values strictly', function () {
    expect(IntTestEnum::hasValue('5'))->toBeFalse()
        ->and(TestEnum::hasValue('Hot-News'))->toBeFalse();
});
