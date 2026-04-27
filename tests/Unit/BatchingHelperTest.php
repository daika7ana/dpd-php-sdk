<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Support\BatchingHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BatchingHelperTest extends TestCase
{
    public function testChunkSplitsListsIntoDeterministicBatches(): void
    {
        $chunks = BatchingHelper::chunk([1, 2, 3, 4, 5], 2);

        $this->assertSame([[1, 2], [3, 4], [5]], $chunks);
    }

    public function testMapChunksProcessesEachBatch(): void
    {
        $resolved = BatchingHelper::mapChunks([1, 2, 3, 4, 5], 2, static fn(array $chunk): int => array_sum($chunk));

        $this->assertSame([3, 7, 5], $resolved);
    }

    public function testChunkRejectsInvalidBatchSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BatchingHelper::chunk([1, 2, 3], 0);
    }
}
