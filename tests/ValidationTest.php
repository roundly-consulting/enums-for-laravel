<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\SeparatorTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;

it('builds an in: validation rule from the backed values', function () {
    expect(TestEnum::validationRule())->toBe('in:hot-news,regular-news,private-news')
        ->and(IntTestEnum::validationRule())->toBe('in:0,5,10');
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

it('builds the rule from case names for pure enums', function () {
    expect(PureTestEnum::validationRule())->toBe('in:Active,Archived');
});

it('accepts every case of a pure enum and rejects anything else', function (string $input, bool $passes) {
    $validator = Validator::make(
        ['status' => $input],
        ['status' => ['required', PureTestEnum::validationRule()]],
    );

    expect($validator->passes())->toBe($passes);
})->with([
    'Active' => ['Active', true],
    'Archived' => ['Archived', true],
    'unknown name' => ['Deleted', false],
]);

it('quotes only the values that collide with the in: syntax', function () {
    expect(SeparatorTestEnum::validationRule())->toBe('in:"a,b",say "hi","""q",c');
});

it('validates backed values containing separators exactly', function (string $input, bool $passes) {
    $validator = Validator::make(
        ['value' => $input],
        ['value' => ['required', SeparatorTestEnum::validationRule()]],
    );

    expect($validator->passes())->toBe($passes);
})->with([
    'comma value' => ['a,b', true],
    'quote value' => ['say "hi"', true],
    'leading quote value' => ['"q', true],
    'plain value' => ['c', true],
    'left half of the comma value' => ['a', false],
    'right half of the comma value' => ['b', false],
    'unquoted leading quote' => ['q', false],
]);
