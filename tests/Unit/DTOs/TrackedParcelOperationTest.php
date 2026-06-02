<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Tracking\Models\TrackedParcelOperation;
use Daika7ana\Dpd\Enums\Tracking\TrackingOperationCode;
use PHPUnit\Framework\TestCase;

final class TrackedParcelOperationTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'dateTime' => '2026-04-28T10:00:00+03:00',
            'operationCode' => 175,
            'description' => 'Predict',
            'exceptionCodes' => [11, 12],
            'place' => 'Bucharest',
            'comment' => 'Left at door',
            'returnShipmentId' => 'RET-001',
            'redirectShipmentId' => 'REDIR-001',
            'consignee' => 'John Doe',
            'podImageURL' => 'https://example.com/pod.jpg',
        ];

        $operation = TrackedParcelOperation::fromArray($data);

        $this->assertSame('2026-04-28T10:00:00+03:00', $operation->dateTime);
        $this->assertSame(175, $operation->operationCode);
        $this->assertSame('Predict', $operation->description);
        $this->assertSame([11, 12], $operation->exceptionCodes);
        $this->assertSame('Bucharest', $operation->place);
        $this->assertSame('Left at door', $operation->comment);
        $this->assertSame('RET-001', $operation->returnShipmentId);
        $this->assertSame('REDIR-001', $operation->redirectShipmentId);
        $this->assertSame('John Doe', $operation->consignee);
        $this->assertSame('https://example.com/pod.jpg', $operation->podImageUrl);
    }

    public function testFromArrayHandlesMissingOptionalFields(): void
    {
        $data = [
            'dateTime' => '2026-04-28',
            'operationCode' => 175,
            'description' => 'Predict',
        ];

        $operation = TrackedParcelOperation::fromArray($data);

        $this->assertSame([], $operation->exceptionCodes);
        $this->assertNull($operation->place);
        $this->assertNull($operation->comment);
        $this->assertNull($operation->returnShipmentId);
        $this->assertNull($operation->redirectShipmentId);
        $this->assertNull($operation->consignee);
        $this->assertNull($operation->podImageUrl);
        $this->assertNull($operation->additionalInfo);
    }

    public function testFromArrayFiltersNonScalarExceptionCodes(): void
    {
        $data = [
            'dateTime' => '2026-04-28',
            'operationCode' => 175,
            'description' => 'Predict',
            'exceptionCodes' => [11, 'invalid', null, 12, [], 13],
        ];

        $operation = TrackedParcelOperation::fromArray($data);

        // 'invalid' is scalar (string) so passes filter but cast to 0; null and [] are non-scalar so filtered out
        $this->assertSame([11, 0, 12, 13], $operation->exceptionCodes);
    }

    public function testResolvedOperationCodeReturnsKnownCode(): void
    {
        $data = [
            'dateTime' => '2026-04-28',
            'operationCode' => 175,
            'description' => 'Predict',
        ];

        $operation = TrackedParcelOperation::fromArray($data);
        $resolved = $operation->resolvedOperationCode();

        $this->assertTrue($resolved->isKnown());
        $this->assertSame(TrackingOperationCode::Predict, $resolved->knownCode);
        $this->assertSame('Predict', $resolved->description);
    }

    public function testResolvedOperationCodeReturnsUnknownForUnmappedCode(): void
    {
        $data = [
            'dateTime' => '2026-04-28',
            'operationCode' => 99999,
            'description' => 'Custom',
        ];

        $operation = TrackedParcelOperation::fromArray($data);
        $resolved = $operation->resolvedOperationCode();

        $this->assertFalse($resolved->isKnown());
        $this->assertNull($resolved->knownCode);
    }

    public function testResolvedExceptionCodesMapsAllCodes(): void
    {
        $data = [
            'dateTime' => '2026-04-28',
            'operationCode' => 44,
            'description' => 'Exception',
            'exceptionCodes' => [11, 1007],
        ];

        $operation = TrackedParcelOperation::fromArray($data);
        $resolved = $operation->resolvedExceptionCodes();

        $this->assertCount(2, $resolved);
        $this->assertSame('Wrong address', $resolved[0]->description);
        $this->assertSame('New date scheduled', $resolved[1]->description);
    }

    public function testResolvedExceptionCodesReturnsEmptyForNoCodes(): void
    {
        $data = [
            'dateTime' => '2026-04-28',
            'operationCode' => 175,
            'description' => 'Predict',
        ];

        $operation = TrackedParcelOperation::fromArray($data);

        $this->assertSame([], $operation->resolvedExceptionCodes());
    }
}
