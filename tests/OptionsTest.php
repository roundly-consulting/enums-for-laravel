<?php

declare(strict_types=1);

use RoundlyConsulting\Enums\DataTransferObjects\EnumOption;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;

it('returns option DTOs with value, label and name', function () {
    $options = TestEnum::options();

    expect($options)->toBeCollection()->toHaveCount(3)
        ->and($options->first())->toBeInstanceOf(EnumOption::class);

    $first = $options->first();

    expect($first->value)->toBe('hot-news')
        ->and($first->label)->toBe('Hot News')
        ->and($first->name)->toBe('HotNews');
});

it('converts an option DTO to an array', function () {
    expect(TestEnum::options()->first()->toArray())->toBe([
        'value' => 'hot-news',
        'label' => 'Hot News',
        'name' => 'HotNews',
    ]);
});

it('uses the name as the value fallback for pure enums', function () {
    $first = PureTestEnum::options()->first();

    expect($first->value)->toBe('Active')
        ->and($first->label)->toBe('Active')
        ->and($first->name)->toBe('Active');
});

it('returns the array form of toOptions', function () {
    expect(TestEnum::toArray())->toBe(TestEnum::toOptions()->all())
        ->and(TestEnum::toArray())->toBe([
            'hot-news' => 'Hot News',
            'regular-news' => 'Regular News',
            'private-news' => 'Private News',
        ]);
});
