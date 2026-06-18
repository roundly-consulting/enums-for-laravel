<?php

declare(strict_types=1);

use RoundlyConsulting\Enums\Tests\TestEnum;

it('returns storable values', function () {
    expect(TestEnum::storable())
        ->toBeCollection()
        ->toArray()->toBe([
            'hot-news',
            'regular-news',
            'private-news',
        ]);
});

it('returns readable string from enum value', function () {
    expect(TestEnum::HotNews->readable())->toBe('Hot News');
});

it('returns options for select elements', function () {
    expect(TestEnum::toOptions())
        ->toBeCollection()
        ->toArray()->toBe([
            'hot-news' => 'Hot News',
            'regular-news' => 'Regular News',
            'private-news' => 'Private News',
        ]);
});

it('checks whether enum is equal to different enum', function () {
    expect(TestEnum::HotNews)->is(TestEnum::HotNews)->toBeTrue()
        ->is(TestEnum::RegularNews)->toBeFalse();
});

it('checks whether enum is not equal to different enum', function () {
    expect(TestEnum::HotNews)->isNot(TestEnum::HotNews)->toBeFalse()
        ->isNot(TestEnum::RegularNews)->toBeTrue();
});

it('checks whether enum is in array of enums', function () {
    expect(TestEnum::HotNews)->isIn([TestEnum::HotNews, TestEnum::RegularNews])->toBeTrue()
        ->isIn([TestEnum::RegularNews, TestEnum::PrivateNews])->toBeFalse();
});

it('checks whether enum is not in array of enums', function () {
    expect(TestEnum::HotNews)->isNotIn([TestEnum::HotNews, TestEnum::RegularNews])->toBeFalse()
        ->isNotIn([TestEnum::RegularNews, TestEnum::PrivateNews])->toBeTrue();
});

it('runs callback when enum is equal to different enum', function () {
    $called = 0;
    $default = 0;

    TestEnum::HotNews->whenIs(TestEnum::HotNews, function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(0);

    TestEnum::HotNews->whenIs(TestEnum::PrivateNews, function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(1);
});

it('runs callback when enum is not equal to different enum', function () {
    $called = 0;
    $default = 0;

    TestEnum::HotNews->whenIsNot(TestEnum::PrivateNews, function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(0);

    TestEnum::HotNews->whenIsNot(TestEnum::HotNews, function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(1);
});

it('runs callback when enum is equal to any enum in array', function () {
    $called = 0;
    $default = 0;

    TestEnum::HotNews->whenIsIn([TestEnum::PrivateNews, TestEnum::HotNews], function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(0);

    TestEnum::HotNews->whenIsIn([TestEnum::PrivateNews], function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(1);
});

it('runs callback when enum is not equal to any enum in array', function () {
    $called = 0;
    $default = 0;

    TestEnum::HotNews->whenIsNotIn([TestEnum::PrivateNews, TestEnum::RegularNews], function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(0);

    TestEnum::HotNews->whenIsNotIn([TestEnum::HotNews], function () use (&$called) {
        $called++;
    }, function () use (&$default) {
        $default++;
    });

    expect($called)
        ->toBe(1)
        ->and($default)
        ->toBe(1);
});

it('is a no-op on non-match when no default callback is given', function () {
    $called = 0;
    $callback = function () use (&$called) {
        $called++;
    };

    TestEnum::HotNews->whenIs(TestEnum::PrivateNews, $callback);
    TestEnum::HotNews->whenIsNot(TestEnum::HotNews, $callback);
    TestEnum::HotNews->whenIsIn([TestEnum::PrivateNews], $callback);
    TestEnum::HotNews->whenIsNotIn([TestEnum::HotNews], $callback);

    expect($called)->toBe(0);
});

it('runs the callback on match when no default callback is given', function () {
    $called = 0;
    $callback = function () use (&$called) {
        $called++;
    };

    TestEnum::HotNews->whenIs(TestEnum::HotNews, $callback);
    TestEnum::HotNews->whenIsNot(TestEnum::PrivateNews, $callback);
    TestEnum::HotNews->whenIsIn([TestEnum::HotNews], $callback);
    TestEnum::HotNews->whenIsNotIn([TestEnum::PrivateNews], $callback);

    expect($called)->toBe(4);
});

it('returns the same instance for fluent chaining', function () {
    $noop = function () {};

    expect(TestEnum::HotNews->whenIs(TestEnum::HotNews, $noop))->toBe(TestEnum::HotNews)
        ->and(TestEnum::HotNews->whenIsNot(TestEnum::PrivateNews, $noop))->toBe(TestEnum::HotNews)
        ->and(TestEnum::HotNews->whenIsIn([TestEnum::HotNews], $noop))->toBe(TestEnum::HotNews)
        ->and(TestEnum::HotNews->whenIsNotIn([TestEnum::PrivateNews], $noop))->toBe(TestEnum::HotNews);
});
