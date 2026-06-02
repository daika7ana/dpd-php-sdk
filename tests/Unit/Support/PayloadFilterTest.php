<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Support;

use Daika7ana\Dpd\Support\PayloadFilter;
use PHPUnit\Framework\TestCase;

final class PayloadFilterTest extends TestCase
{
    public function testRemoveNullsStripsNullValues(): void
    {
        $payload = ['a' => 1, 'b' => null, 'c' => 'hello', 'd' => null];

        $result = PayloadFilter::removeNulls($payload);

        $this->assertSame(['a' => 1, 'c' => 'hello'], $result);
    }

    public function testRemoveNullsKeepsFalsyValues(): void
    {
        $payload = ['a' => 0, 'b' => '', 'c' => false, 'd' => null];

        $result = PayloadFilter::removeNulls($payload);

        $this->assertSame(['a' => 0, 'b' => '', 'c' => false], $result);
    }

    public function testRemoveNullsAndEmptyArraysStripsNullsAndEmptyArrays(): void
    {
        $payload = ['a' => 1, 'b' => null, 'c' => [], 'd' => [1, 2]];

        $result = PayloadFilter::removeNullsAndEmptyArrays($payload);

        $this->assertSame(['a' => 1, 'd' => [1, 2]], $result);
    }

    public function testRemoveNullsAndEmptyArraysKeepsFalsyValues(): void
    {
        $payload = ['a' => 0, 'b' => '', 'c' => false, 'd' => null, 'e' => []];

        $result = PayloadFilter::removeNullsAndEmptyArrays($payload);

        $this->assertSame(['a' => 0, 'b' => '', 'c' => false], $result);
    }
}
