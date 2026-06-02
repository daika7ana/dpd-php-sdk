<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Cache;

use Daika7ana\Dpd\Cache\NullReferenceDataCache;
use PHPUnit\Framework\TestCase;

final class NullReferenceDataCacheTest extends TestCase
{
    public function testGetAlwaysReturnsNull(): void
    {
        $cache = new NullReferenceDataCache();

        $this->assertNull($cache->get('any-key'));
    }

    public function testHasAlwaysReturnsFalse(): void
    {
        $cache = new NullReferenceDataCache();

        $this->assertFalse($cache->has('any-key'));
    }

    public function testRememberAlwaysCallsResolver(): void
    {
        $cache = new NullReferenceDataCache();

        $callCount = 0;
        $result = $cache->remember('key', static function () use (&$callCount): string {
            $callCount++;

            return 'resolved';
        });

        $this->assertSame('resolved', $result);
        $this->assertSame(1, $callCount);

        // Call again - should resolve again
        $result2 = $cache->remember('key', static function () use (&$callCount): string {
            $callCount++;

            return 'resolved-2';
        });

        $this->assertSame('resolved-2', $result2);
        $this->assertSame(2, $callCount);
    }

    public function testPutDoesNotThrow(): void
    {
        $cache = new NullReferenceDataCache();

        // Should not throw
        $cache->put('key', 'value');

        $this->assertFalse($cache->has('key'));
    }

    public function testForgetDoesNotThrow(): void
    {
        $cache = new NullReferenceDataCache();

        $cache->forget('key');

        // Still returns false after forget
        $this->assertFalse($cache->has('key'));
    }

    public function testClearDoesNotThrow(): void
    {
        $cache = new NullReferenceDataCache();

        $cache->clear();

        // Still returns false after clear
        $this->assertFalse($cache->has('key'));
    }
}
