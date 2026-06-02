<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Cache;

use Daika7ana\Dpd\Cache\ArrayReferenceDataCache;
use PHPUnit\Framework\TestCase;

final class ArrayReferenceDataCacheTest extends TestCase
{
    public function testGetReturnsNullForMissingKey(): void
    {
        $cache = new ArrayReferenceDataCache();

        $this->assertNull($cache->get('missing'));
    }

    public function testPutAndGetRoundTrip(): void
    {
        $cache = new ArrayReferenceDataCache();

        $cache->put('key', 'value');

        $this->assertSame('value', $cache->get('key'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $cache = new ArrayReferenceDataCache();

        $this->assertFalse($cache->has('missing'));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        $cache = new ArrayReferenceDataCache();

        $cache->put('key', 'value');

        $this->assertTrue($cache->has('key'));
    }

    public function testForgetRemovesKey(): void
    {
        $cache = new ArrayReferenceDataCache();
        $cache->put('key', 'value');

        $cache->forget('key');

        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testClearRemovesAllKeys(): void
    {
        $cache = new ArrayReferenceDataCache();
        $cache->put('key1', 'value1');
        $cache->put('key2', 'value2');

        $cache->clear();

        $this->assertFalse($cache->has('key1'));
        $this->assertFalse($cache->has('key2'));
    }

    public function testRememberReturnsCachedValueOnHit(): void
    {
        $cache = new ArrayReferenceDataCache();
        $cache->put('key', 'cached');

        $callCount = 0;
        $result = $cache->remember('key', static function () use (&$callCount): string {
            $callCount++;

            return 'resolved';
        });

        $this->assertSame('cached', $result);
        $this->assertSame(0, $callCount);
    }

    public function testRememberCallsResolverOnMiss(): void
    {
        $cache = new ArrayReferenceDataCache();

        $result = $cache->remember('key', static fn(): string => 'resolved');

        $this->assertSame('resolved', $result);
        $this->assertSame('resolved', $cache->get('key'));
    }

    public function testTtlExpirationRemovesExpiredEntries(): void
    {
        $cache = new ArrayReferenceDataCache();

        // Put with 1 second TTL
        $cache->put('key', 'value', 1);

        // Should exist immediately
        $this->assertTrue($cache->has('key'));

        // Wait for expiration — use 3s to avoid edge cases with time() rounding
        sleep(3);

        // Should be expired
        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testNoTtlMeansPersistentEntry(): void
    {
        $cache = new ArrayReferenceDataCache();

        $cache->put('key', 'value');

        // Should still exist after a short delay
        usleep(100_000); // 100ms
        $this->assertTrue($cache->has('key'));
        $this->assertSame('value', $cache->get('key'));
    }
}
