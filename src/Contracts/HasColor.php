<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums\Contracts;

/**
 * An enum whose cases carry a display colour, read by presentation() and presentations().
 *
 * The colour is a free-form token the front end understands — a badge variant, a CSS class
 * or a hex code. The package passes it through and never interprets it.
 *
 * It is an interface, not a trait method, so an enum that already declares a color() of its
 * own (with any return type) keeps it until it chooses to implement this.
 */
interface HasColor
{
    public function color(): string;
}
