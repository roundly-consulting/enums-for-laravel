<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use RoundlyConsulting\Enums\Exceptions\EnumException;
use RoundlyConsulting\Enums\Tests\BackslashTestEnum;
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

it('builds a rule that reads back as exactly the values when they hold backslashes', function () {
    $rule = BackslashTestEnum::validationRule();

    expect($rule)->toBe('in:"C:\a,b"\,"x\"y,z",z')
        ->and(str_getcsv(substr($rule, 3), escape: '\\'))->toBe(['C:\a,b\\', 'x\"y,z', 'z']);
});

it('validates backed values holding backslashes exactly', function (string $input, bool $passes) {
    $validator = Validator::make(
        ['value' => $input],
        ['value' => ['required', BackslashTestEnum::validationRule()]],
    );

    expect($validator->passes())->toBe($passes);
})->with([
    'trailing backslash value' => ['C:\a,b\\', true],
    'escaped quote value' => ['x\"y,z', true],
    'plain value' => ['z', true],
    'trailing backslash value glued to the next one' => ['C:\a,b\",z', false],
    'left half of the escaped quote value' => ['x\"y', false],
    'right half of the escaped quote value' => ['z"', false],
]);

it('names the enum and the value it cannot write into the rule', function () {
    expect(EnumException::valueNotRepresentable(BackslashTestEnum::class, 'C:\a,b\\'))
        ->toBeInstanceOf(EnumException::class)
        ->getMessage()->toBe('Value [C:\a,b\\] of enum ['.BackslashTestEnum::class.'] cannot be written into an in: rule that reads back unchanged; validate it with Rule::enum() instead.');
});
