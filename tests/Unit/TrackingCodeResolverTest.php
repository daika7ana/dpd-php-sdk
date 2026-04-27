<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Enums\Tracking\TrackingOperationCode;
use Daika7ana\Dpd\Support\TrackingExceptionCodeResolver;
use Daika7ana\Dpd\Support\TrackingOperationCodeResolver;
use PHPUnit\Framework\TestCase;

final class TrackingCodeResolverTest extends TestCase
{
    public function testTrackingOperationResolverReturnsKnownEnumBackedDescriptions(): void
    {
        $resolved = TrackingOperationCodeResolver::resolve(175);

        $this->assertTrue($resolved->isKnown());
        $this->assertSame('Predict', $resolved->description);
        $this->assertSame(TrackingOperationCode::Predict, $resolved->knownCode);
    }

    public function testTrackingOperationResolverFallsBackForUnknownCodes(): void
    {
        $resolved = TrackingOperationCodeResolver::resolve(9999);

        $this->assertFalse($resolved->isKnown());
        $this->assertSame('Unknown tracking operation code', $resolved->description);
    }

    public function testTrackingExceptionResolverReturnsMappedOperations(): void
    {
        $resolved = TrackingExceptionCodeResolver::resolve(1007);

        $this->assertTrue($resolved->isKnown());
        $this->assertSame('New date scheduled', $resolved->description);
        $this->assertSame([44, 136, 164], $resolved->operationCodes);
    }

    public function testTrackingExceptionResolverFallsBackForUnknownCodes(): void
    {
        $resolved = TrackingExceptionCodeResolver::resolve(987654);

        $this->assertFalse($resolved->isKnown());
        $this->assertSame([], $resolved->operationCodes);
    }
}
