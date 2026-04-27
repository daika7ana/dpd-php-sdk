<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements IteratorAggregate<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
final class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param array<TKey, TValue> $items
     */
    public function __construct(
        private array $items,
    ) {}

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return TValue|null
     */
    public function last(): mixed
    {
        $lastKey = array_key_last($this->items);

        if ($lastKey === null) {
            return null;
        }

        return $this->items[$lastKey];
    }

    /**
     * @param TKey|null $key
     *
     * @return array<TKey, TValue>|TValue|null
     */
    public function get(int|string|null $key = null): mixed
    {
        if ($key === null) {
            return $this->items;
        }

        return $this->items[$key] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @param TKey $key
     */
    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @return Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return TValue|null
     */
    public function first(): mixed
    {
        $firstKey = array_key_first($this->items);

        if ($firstKey === null) {
            return null;
        }

        return $this->items[$firstKey];
    }

    /**
     * @return static<int, TValue>
     */
    public function values(): static
    {
        /** @var list<TValue> $values */
        $values = array_values($this->items);

        return new self($values);
    }

    /**
     * @return static<int, TKey>
     */
    public function keys(): static
    {
        /** @var list<TKey> $keys */
        $keys = array_keys($this->items);

        return new self($keys);
    }

    /**
     * @param callable(TValue, TKey): bool|null $callback
     *
     * @return static<TKey, TValue>
     */
    public function filter(?callable $callback = null): static
    {
        if ($callback === null) {
            /** @var array<TKey, TValue> $filtered */
            $filtered = array_filter($this->items);

            return new self($filtered);
        }

        $filtered = [];

        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                $filtered[$key] = $item;
            }
        }

        return new self($filtered);
    }

    /**
     * @template TMappedValue
     *
     * @param callable(TValue, TKey): TMappedValue $callback
     *
     * @return static<TKey, TMappedValue>
     */
    public function map(callable $callback): static
    {
        $mapped = [];

        foreach ($this->items as $key => $item) {
            $mapped[$key] = $callback($item, $key);
        }

        return new self($mapped);
    }

    /**
     * @template TMappedKey of array-key
     * @template TMappedValue
     *
     * @param callable(TValue, TKey): array<TMappedKey, TMappedValue> $callback
     *
     * @return static<TMappedKey, TMappedValue>
     */
    public function mapWithKeys(callable $callback): static
    {
        $mapped = [];

        foreach ($this->items as $key => $item) {
            foreach ($callback($item, $key) as $mappedKey => $mappedValue) {
                $mapped[$mappedKey] = $mappedValue;
            }
        }

        return new self($mapped);
    }

    /**
     * @param string|int $valueKey
     * @param string|int|null $keyBy
     *
     * @return static<array-key, mixed>
     */
    public function pluck(string|int $valueKey, string|int|null $keyBy = null): static
    {
        $plucked = [];

        foreach ($this->items as $index => $item) {
            $value = self::pluckValue($item, $valueKey);

            if ($keyBy === null) {
                $plucked[$index] = $value;

                continue;
            }

            $mappedKey = self::pluckValue($item, $keyBy);

            if (is_int($mappedKey) || is_string($mappedKey)) {
                $plucked[$mappedKey] = $value;

                continue;
            }

            $plucked[$index] = $value;
        }

        return new self($plucked);
    }

    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_int($offset) || is_string($offset)
            ? array_key_exists($offset, $this->items)
            : false;
    }

    /**
     * @param TKey $offset
     *
     * @return TValue|null
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!is_int($offset) && !is_string($offset)) {
            return null;
        }

        return $this->items[$offset] ?? null;
    }

    /**
     * @param TKey|null $offset
     * @param TValue $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;

            return;
        }

        if (!is_int($offset) && !is_string($offset)) {
            return;
        }

        $this->items[$offset] = $value;
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!is_int($offset) && !is_string($offset)) {
            return;
        }

        unset($this->items[$offset]);
    }

    private static function pluckValue(mixed $item, string|int $key): mixed
    {
        if (is_array($item)) {
            return $item[$key] ?? null;
        }

        if (is_object($item)) {
            return $item->{$key} ?? null;
        }

        return null;
    }
}
