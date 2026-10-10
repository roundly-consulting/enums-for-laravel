# Changelog

All notable changes to `enums-for-laravel` are documented in this file. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[Semantic Versioning](https://semver.org/).

## Unreleased

## 1.1.0 - 2026-10-10

### Added

- `#[TranslatedLabels]` attribute: an enum that carries it reads each label from a translation
  group, `<group>.<value>` (the case name for pure enums), instead of its headline. The default
  group is `enums.` plus the snake_case class name (`OrderStatus` reads `enums.order_status.*`);
  pass one for anything else, including a package's own files (`'billing::enums.order_status'`).
  Every list built on labels (`labels()`, `toOptions()`, `toArray()`, `options()`, `fromLabel()`)
  translates with it. A case with no line falls back to `fallback_locale`, then to the headline
  label as before. Enums without the attribute are unchanged.
- `untranslated(?string $locale = null)`: the cases of a `#[TranslatedLabels]` enum that have no
  line in exactly that locale, for a test such as
  `expect(OrderStatus::untranslated('sk'))->toBeEmpty()`.
- `EnumException::invalidLabelGroup()`, thrown when the attribute names a blank group or one that
  starts or ends with whitespace, `.` or `:`, and `EnumException::labelsNotTranslated()`, thrown by
  `untranslated()` on an enum without the attribute.
- `HasColor` contract (`color(): string`) for enums whose cases carry a display colour: a badge
  variant, CSS class or hex code, passed through as-is. It is opt-in, so an enum's existing
  `color()` method keeps working until the enum implements it.
- `presentation()` and `presentations()`: one case, or every case in declaration order, as an
  `EnumPresentation` DTO (`value`, translated `label`, `color`). It serialises to
  `{value, label, color}` through `toArray()` / `json_encode()`; `color` is `null` without
  `HasColor`. `options()` keeps its `{value, label, name}` shape.

### Changed

- Documentation: `readable()` is the one method to override for a custom label; `label()` is its
  alias, and the lists read `readable()`, so overriding `label()` alone leaves them untranslated.

## 1.0.1 - 2026-10-05

### Changed

- Labels no longer drop a `0` or a minus sign. In `readable()` / `label()` and everything built on
  them (`labels()`, `toOptions()`, `toArray()`, `options()`, `fromLabel()`), an int `0` is
  labelled `'0'` instead of `''`, and a negative int keeps its sign, so `-1` no longer collides
  with `1`. A `0` part of a string value or case name is kept too: `'level-0'` is `'Level 0'`,
  not `'Level'`. Every other label is unchanged. Upgrade: re-key any translation keyed on an old
  label (the blank `''` for `0`, `'1'` for `-1`, `'Level'` for `'level-0'`).
- `random()` on an enum with no cases throws `EnumException::noCases()` instead of PHP's
  `ValueError`, so it can be caught along with the package's other errors. Upgrade: catch
  `EnumException` where you caught that `ValueError`.
- Maintenance: `composer.json` `homepage` and `support.docs` now point to the documentation site.
- Documentation: `fromLabel()` and `tryFromLabel()` now state that when several cases share a label,
  the first declared case wins.
- Documentation: the README banner uses an absolute image URL, so it also renders on Packagist and
  other sites.

### Fixed

- `validationRule()` now reads back exactly for string values that need quoting and have a backslash
  before a quote or at the end (e.g. `x\"y,z`, `C:\a,b\`). Before, the rule rejected those values
  and could accept a value that isn't in the enum. Rules for every other enum are unchanged. If a
  value ever had no CSV form that reads back unchanged, the method would throw
  `EnumException::valueNotRepresentable()` rather than return a wrong rule.

## 1.0.0 - 2026-10-03

Initial public release.

### Added

- A `Helpers` trait for any PHP enum — full support for backed enums, with pure enums falling
  back to the case name.
- Translatable, human-readable labels with `readable()` / `label()`.
- `names()`, `values()`, `labels()`, `storable()`, `collect()` and `count()` accessors, in
  declaration order.
- Select-ready option lists: `toOptions()` / `toArray()` (`value => label`) and `options()`
  returning typed `EnumOption` objects for JS front ends.
- Case lookups by name or label (`fromName()`, `tryFromName()`, `fromLabel()`, `tryFromLabel()`)
  and existence checks (`hasName()`, `hasValue()`).
- `validationRule()`, an `in:` rule built from `values()` so it never drifts from the enum (values
  that would break the rule's comma syntax are quoted).
- `random()` for factories, seeders and tests.
- Equality checks (`is()`, `isNot()`, `isIn()`, `isNotIn()`) and fluent conditional callbacks
  (`whenIs()`, `whenIsNot()`, `whenIsIn()`, `whenIsNotIn()`).
