# Enums for Laravel

Convenient helper methods for PHP enums in Laravel applications.

Drop the `Helpers` trait into any backed enum to get readable labels, select-ready
option lists, expressive equality checks, and fluent conditional callbacks — all backed
by Laravel's `Str` and translation helpers.

## Requirements

- PHP `^8.3`
- Laravel `^12.0` or `^13.0` (via `illuminate/contracts`)

## Installation

Install the package via Composer:

```bash
composer require roundly-consulting/enums-for-laravel
```

There is nothing to publish or migrate — this is a trait-only package with no service
provider, config, or migrations.

## Usage

Add the `RoundlyConsulting\Enums\Helpers` trait to any backed (string or int) enum:

```php
use RoundlyConsulting\Enums\Helpers;

enum NewsCategory: string
{
    use Helpers;

    case HotNews = 'hot-news';
    case RegularNews = 'regular-news';
    case PrivateNews = 'private-news';
}
```

### Readable labels

`readable()` turns a case value into a human-friendly, translated label using
`Str::headline()`. Because it passes through Laravel's `__()` helper, you can localise
labels via your translation files.

```php
NewsCategory::HotNews->readable(); // "Hot News"
```

### Storable values

`storable()` returns a collection of the raw backed values, in declaration order — handy
for validation rules or persisting allowed values.

```php
NewsCategory::storable();
// Illuminate\Support\Collection of ['hot-news', 'regular-news', 'private-news']
```

### Select options

`toOptions()` returns a `value => label` collection, ready for a `<select>` element or a
form component.

```php
NewsCategory::toOptions();
// ['hot-news' => 'Hot News', 'regular-news' => 'Regular News', 'private-news' => 'Private News']
```

### Equality checks

```php
$category = NewsCategory::HotNews;

$category->is(NewsCategory::HotNews);    // true
$category->isNot(NewsCategory::HotNews); // false

$category->isIn([NewsCategory::HotNews, NewsCategory::RegularNews]);    // true
$category->isNotIn([NewsCategory::RegularNews, NewsCategory::PrivateNews]); // true
```

### Conditional callbacks

The `whenIs*` methods run a callback only when the condition matches, with an optional
default callback otherwise. Each returns the enum instance, so calls remain fluent.

```php
NewsCategory::HotNews
    ->whenIs(NewsCategory::HotNews, fn ($enum) => activate($enum))
    ->whenIsNot(NewsCategory::PrivateNews, fn ($enum) => publish($enum));

NewsCategory::HotNews->whenIsIn(
    [NewsCategory::HotNews, NewsCategory::RegularNews],
    fn ($enum) => notifySubscribers($enum),
    fn ($enum) => archive($enum), // runs when the condition is false
);

NewsCategory::HotNews->whenIsNotIn(
    [NewsCategory::PrivateNews],
    fn ($enum) => index($enum),
);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.
