# Changelog

All notable changes to `enums-for-laravel` are documented in this file. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[Semantic Versioning](https://semver.org/).

## Unreleased

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
