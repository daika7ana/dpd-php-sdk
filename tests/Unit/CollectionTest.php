<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testCollectionSupportsCommonTraversalHelpers(): void
    {
        $collection = new Collection([
            'ro' => ['id' => 642, 'name' => 'Romania'],
            'bg' => ['id' => 100, 'name' => 'Bulgaria'],
        ]);

        $this->assertSame('Romania', $collection->first()['name']);
        $this->assertSame('Bulgaria', $collection->last()['name']);
        $this->assertTrue($collection->has('ro'));
        $this->assertSame(642, $collection->pluck('id')->first());
        $this->assertSame(['ro', 'bg'], $collection->keys()->all());
        $this->assertSame(['Romania', 'Bulgaria'], $collection->pluck('name')->values()->all());
        $this->assertSame('Romania', $collection['ro']['name']);
    }

    public function testCollectionMapAndFilterPreserveTypeFriendlyShape(): void
    {
        $collection = new Collection([1, 2, 3, 4]);

        $mapped = $collection->filter(static fn(int $value): bool => $value % 2 === 0)
            ->map(static fn(int $value): int => $value * 10)
            ->values();

        $this->assertSame([20, 40], $mapped->all());
    }
}
