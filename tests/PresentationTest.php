<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Lang;
use RoundlyConsulting\Enums\DataTransferObjects\EnumPresentation;
use RoundlyConsulting\Enums\Tests\Fixtures\BadgeStatus;
use RoundlyConsulting\Enums\Tests\Fixtures\CustomLabel;
use RoundlyConsulting\Enums\Tests\Fixtures\Lane;
use RoundlyConsulting\Enums\Tests\Fixtures\LegacyColor;
use RoundlyConsulting\Enums\Tests\Fixtures\Severity;
use RoundlyConsulting\Enums\Tests\IntTestEnum;
use RoundlyConsulting\Enums\Tests\PureTestEnum;
use RoundlyConsulting\Enums\Tests\TestEnum;

beforeEach(function () {
    Lang::addLines([
        'enums.badge_status.draft' => 'Draft',
        'enums.badge_status.live' => 'Published',
        'enums.badge_status.archived' => 'Archived',
    ], 'en');
    Lang::addLines([
        'enums.badge_status.draft' => 'Koncept',
        'enums.badge_status.live' => 'Zverejnené',
        'enums.badge_status.archived' => 'Archivované',
    ], 'sk');
});

it('presents a case with its value, translated label and colour', function () {
    $presentation = BadgeStatus::Live->presentation();

    expect($presentation)->toBeInstanceOf(EnumPresentation::class)
        ->and($presentation->value)->toBe('live')
        ->and($presentation->label)->toBe('Published')
        ->and($presentation->color)->toBe('success');
});

it('presents int and pure enums with HasColor', function () {
    expect(Severity::High->presentation()->toArray())->toBe(['value' => 9, 'label' => '9', 'color' => 'danger'])
        ->and(Lane::Fast->presentation()->toArray())->toBe(['value' => 'Fast', 'label' => 'Fast', 'color' => 'fast']);
});

it('presents a null colour without HasColor', function () {
    expect(TestEnum::HotNews->presentation()->toArray())->toBe(['value' => 'hot-news', 'label' => 'Hot News', 'color' => null])
        ->and(IntTestEnum::Low->presentation()->toArray())->toBe(['value' => 0, 'label' => '0', 'color' => null])
        ->and(PureTestEnum::Active->presentation()->toArray())->toBe(['value' => 'Active', 'label' => 'Active', 'color' => null]);
});

it('never reads a color() the enum declares without implementing HasColor', function () {
    expect(LegacyColor::Red->presentation()->color)->toBeNull()
        ->and(LegacyColor::Red->color())->toBe([255, 0, 0]);
});

it('serialises as value, label, color in that order', function () {
    expect(BadgeStatus::Draft->presentation())->toBeInstanceOf(Arrayable::class)->toBeInstanceOf(JsonSerializable::class)
        ->and(array_keys(BadgeStatus::Draft->presentation()->toArray()))->toBe(['value', 'label', 'color'])
        ->and(json_encode(BadgeStatus::Draft->presentation()))->toBe('{"value":"draft","label":"Draft","color":"secondary"}')
        ->and(json_encode(IntTestEnum::Medium->presentation()))->toBe('{"value":5,"label":"5","color":null}')
        ->and(BadgeStatus::Draft->presentation()->jsonSerialize())->toBe(BadgeStatus::Draft->presentation()->toArray());
});

it('presents every case in declaration order with translated labels', function () {
    app()->setLocale('sk');

    $presentations = BadgeStatus::presentations();

    expect($presentations)->toBeCollection()->toHaveCount(3)
        ->and($presentations->keys()->all())->toBe([0, 1, 2])
        ->and($presentations->map->toArray()->all())->toBe([
            ['value' => 'draft', 'label' => 'Koncept', 'color' => 'secondary'],
            ['value' => 'live', 'label' => 'Zverejnené', 'color' => 'success'],
            ['value' => 'archived', 'label' => 'Archivované', 'color' => '#6b7280'],
        ])
        ->and(json_encode($presentations))->toBe('[{"value":"draft","label":"Koncept","color":"secondary"},{"value":"live","label":"Zverejnen\\u00e9","color":"success"},{"value":"archived","label":"Archivovan\\u00e9","color":"#6b7280"}]');
});

it('labels through readable(), so its override reaches every presentation', function () {
    expect(CustomLabel::Ok->presentation()->label)->toBe('Custom ok')
        ->and(CustomLabel::presentations()->map->label->all())->toBe(['Custom ok']);
});

it('keeps options() at value, label, name on an enum with a colour', function () {
    expect(BadgeStatus::options()->first()->toArray())->toBe(['value' => 'draft', 'label' => 'Draft', 'name' => 'Draft'])
        ->and(BadgeStatus::toArray())->toBe(['draft' => 'Draft', 'live' => 'Published', 'archived' => 'Archived']);
});
