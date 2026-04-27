<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use InvalidArgumentException;

final class BatchingHelper
{
    /**
     * @template TItem
     *
     * @param list<TItem> $items
     *
     * @return list<list<TItem>>
     */
    public static function chunk(array $items, int $batchSize): array
    {
        if ($batchSize < 1) {
            throw new InvalidArgumentException('Batch size must be greater than zero.');
        }

        /** @var list<list<TItem>> $chunks */
        $chunks = array_chunk($items, $batchSize);

        return $chunks;
    }

    /**
     * @template TItem
     * @template TResolved
     *
     * @param list<TItem> $items
     * @param callable(list<TItem>): TResolved $callback
     *
     * @return list<TResolved>
     */
    public static function mapChunks(array $items, int $batchSize, callable $callback): array
    {
        $resolved = [];

        foreach (self::chunk($items, $batchSize) as $chunk) {
            $resolved[] = $callback($chunk);
        }

        return $resolved;
    }
}
