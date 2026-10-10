<?php

declare(strict_types=1);

use RoundlyConsulting\Enums\Helpers;
use RoundlyConsulting\Enums\Tests\Fixtures\Bc\Snapshot;

it('reproduces the 1.0.1 output exactly for enums without #[TranslatedLabels]', function () {
    expect(Snapshot::take())->toBe(require __DIR__.'/Fixtures/Bc/golden-1.0.1.php');
});

it('adds trait methods only under names no fleet enum declares', function () {
    $methods = array_map(
        static fn (ReflectionMethod $method): string => $method->getName(),
        (new ReflectionClass(Helpers::class))->getMethods(),
    );
    sort($methods);

    // Every trait method name is a BC surface: a host enum's own method of the same name
    // silently replaces it. 1.0.1's set, plus only the names scanned clean across the fleet.
    expect($methods)->toBe([
        'backing', 'collect', 'count', 'fromLabel', 'fromName', 'hasName', 'hasValue', 'is',
        'isIn', 'isNot', 'isNotIn', 'label', 'labels', 'names', 'options', 'random', 'readable',
        'storable', 'toArray', 'toOptions', 'tryFromLabel', 'tryFromName', 'untranslated',
        'validationRule', 'values', 'whenIs', 'whenIsIn', 'whenIsNot', 'whenIsNotIn',
    ]);
});
