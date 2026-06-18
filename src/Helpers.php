<?php

declare(strict_types=1);

namespace RoundlyConsulting\Enums;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Convenience helpers for backed enums.
 *
 * Intended to be used by a string- or int-backed enum:
 *
 * ```php
 * enum Status: string
 * {
 *     use Helpers;
 * }
 * ```
 */
trait Helpers
{
    /**
     * The raw backed values of every case, in declaration order.
     *
     * @return Collection<int, value-of<static>>
     */
    public static function storable(): Collection
    {
        return (new Collection(static::cases()))->pluck('value');
    }

    /**
     * A value => readable-label map for every case, suitable for select inputs.
     *
     * @return Collection<array-key, string>
     */
    public static function toOptions(): Collection
    {
        return (new Collection(static::cases()))->mapWithKeys(static fn (self $enum): array => [
            $enum->value => $enum->readable(),
        ]);
    }

    /**
     * A human-friendly, translated label derived from the case value.
     */
    public function readable(): string
    {
        return (string) __(Str::headline((string) $this->value));
    }

    public function is(self $enum): bool
    {
        return $this === $enum;
    }

    public function isNot(self $enum): bool
    {
        return ! $this->is($enum);
    }

    /**
     * @param  array<int, self>  $enums
     */
    public function isIn(array $enums): bool
    {
        foreach ($enums as $enum) {
            if ($this === $enum) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, self>  $enums
     */
    public function isNotIn(array $enums): bool
    {
        return ! $this->isIn($enums);
    }

    /**
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIs(self $enum, Closure $callback, ?Closure $default = null): self
    {
        if ($this->is($enum)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIsNot(self $enum, Closure $callback, ?Closure $default = null): self
    {
        if ($this->isNot($enum)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * @param  array<int, self>  $enums
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIsIn(array $enums, Closure $callback, ?Closure $default = null): self
    {
        if ($this->isIn($enums)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }

    /**
     * @param  array<int, self>  $enums
     * @param  Closure(self): void  $callback
     * @param  (Closure(self): void)|null  $default
     */
    public function whenIsNotIn(array $enums, Closure $callback, ?Closure $default = null): self
    {
        if ($this->isNotIn($enums)) {
            $callback($this);
        } elseif (! is_null($default)) {
            $default($this);
        }

        return $this;
    }
}
