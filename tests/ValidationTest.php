<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use RoundlyConsulting\Enums\Tests\TestEnum;

it('builds an in: validation rule from the backed values', function () {
    expect(TestEnum::validationRule())->toBe('in:hot-news,regular-news,private-news');
});

it('passes validation for an allowed value', function () {
    $validator = Validator::make(
        ['category' => 'hot-news'],
        ['category' => [TestEnum::validationRule()]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails validation for a disallowed value', function () {
    $validator = Validator::make(
        ['category' => 'breaking-news'],
        ['category' => [TestEnum::validationRule()]],
    );

    expect($validator->fails())->toBeTrue();
});
