<?php

declare(strict_types=1);

use RoundlyConsulting\Enums\Exceptions\EnumException;
use RoundlyConsulting\Testing\Arch\ArchPresets;

/**
 * Enums shipped with exactly one arch test (a dd/dump/ray ban), so every preset below
 * except the last is a new guard rather than a replacement.
 *
 * This package is a Tier-0 leaf: no service provider, no config file, no migrations, no
 * models — a trait, a DTO and an exception. Two of the seven presets therefore do not
 * apply and are deliberately **not** registered rather than added for symmetry:
 *
 *   - `swappableModelsAreNotFinal` — there is no config-swappable model, and no config
 *     file to swap one through. The preset takes a non-empty map; a package with nothing
 *     to map has nothing to assert.
 *   - `modelsResolveThroughSeam` — enums ships no Eloquent model, no `*_model` config key
 *     and no `Support` seam, so both of its halves (the late-static-binding ban and the
 *     stray-swap-literal scan) would run against a package that cannot express either
 *     bug. It would be green on the first run and green forever, which is the vacuous
 *     green this whole adoption exists to kill. jwt rejected it for the same reason;
 *     shops and credits adopted it because they have the shape it is aimed at.
 */
ArchPresets::strictTypes('RoundlyConsulting\Enums');

/**
 * One deliberate extension point is exempt: EnumException, the base every enums error
 * extends so a host can catch them uniformly (and whose named constructors a host may
 * want to specialise). EnumOption stays final; Helpers is a trait, which `classes()`
 * does not consider.
 */
ArchPresets::finalByDefault('RoundlyConsulting\Enums', [EnumException::class]);

/**
 * Enums does no cryptography. The ban is a standing guard against a hash- or
 * random-based case lookup being hand-rolled here rather than in crypto-for-laravel —
 * `Helpers` is a trait 45 packages mix into their own enums, so a primitive landing here
 * would land everywhere.
 */
ArchPresets::noLocalCryptoPrimitives('RoundlyConsulting\Enums');

/**
 * The Dependency Policy as a test, and the assertion that matters most on a leaf: enums
 * sits in the `require` of most of the fleet, so a third-party vendor entering here ships
 * transitively into every consumer of every consumer. No `alsoAllow` — enums' `require`
 * is php + illuminate/{contracts,container,support}, and the workflow installs test
 * tooling with `--dev`, so nothing legitimately lands in `require` that this must
 * forgive. If it goes red the graph is wrong; never widen the allow-list to quiet it.
 */
ArchPresets::runtimeRequireIsWhitelisted(__DIR__.'/../composer.json');

ArchPresets::noDebuggingLeftovers();

/**
 * The runtime `require` is php + illuminate/{contracts,container,support}. The foundation
 * helpers (`__()`, `trans()`, `app()`, …) live in laravel/framework, which enums does not
 * declare — using one would make the trait fatal wherever only the declared deps exist.
 */
arch('uses no laravel/framework foundation helpers')
    ->expect('RoundlyConsulting\Enums')
    ->not->toUse(['__', 'trans', 'trans_choice', 'app', 'resolve', 'config', 'lang_path']);
