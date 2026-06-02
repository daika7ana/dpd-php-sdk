<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Tracking\Models\TrackedParcel;
use PHPUnit\Framework\TestCase;

final class TrackedParcelTest extends TestCase
{
    public function testFromArrayMapsParcelIdAndOperations(): void
    {
        $data = [
            'parcelId' => 'P001',
            'operations' => [
                ['dateTime' => '2026-04-28', 'operationCode' => 175, 'description' => 'Predict'],
                ['dateTime' => '2026-04-29', 'operationCode' => 44, 'description' => 'Delivered'],
            ],
            'externalCarrierParcelNumbers' => ['EXT-001', 'EXT-002'],
        ];

        $parcel = TrackedParcel::fromArray($data);

        $this->assertSame('P001', $parcel->parcelId);
        $this->assertCount(2, $parcel->operations);
        $this->assertSame(175, $parcel->operations->first()?->operationCode);
        $this->assertCount(2, $parcel->externalCarrierParcelNumbers);
        $this->assertSame('EXT-001', $parcel->externalCarrierParcelNumbers->first());
    }

    public function testFromArrayHandlesMissingOptionalFields(): void
    {
        $data = ['parcelId' => 'P002'];

        $parcel = TrackedParcel::fromArray($data);

        $this->assertSame('P002', $parcel->parcelId);
        $this->assertCount(0, $parcel->operations);
        $this->assertCount(0, $parcel->externalCarrierParcelNumbers);
        $this->assertSame([], $parcel->externalCarrierParcelNumbersDetails);
        $this->assertNull($parcel->error);
    }

    public function testFromArrayMapsError(): void
    {
        $data = [
            'parcelId' => 'P003',
            'error' => ['code' => 404, 'message' => 'Not found', 'id' => 'err-404'],
        ];

        $parcel = TrackedParcel::fromArray($data);

        $this->assertNotNull($parcel->error);
        $this->assertSame(404, $parcel->error->code);
        $this->assertSame('Not found', $parcel->error->message);
    }

    public function testFromArrayFiltersNonArrayOperations(): void
    {
        $data = [
            'parcelId' => 'P004',
            'operations' => [
                ['dateTime' => '2026-04-28', 'operationCode' => 175, 'description' => 'Predict'],
                'invalid',
                null,
            ],
        ];

        $parcel = TrackedParcel::fromArray($data);

        $this->assertCount(1, $parcel->operations);
    }

    public function testFromArrayFiltersNonScalarExternalCarrierNumbers(): void
    {
        $data = [
            'parcelId' => 'P005',
            'externalCarrierParcelNumbers' => ['EXT-001', null, 123, [], 'EXT-002'],
        ];

        $parcel = TrackedParcel::fromArray($data);

        $this->assertCount(3, $parcel->externalCarrierParcelNumbers);
        $this->assertSame('EXT-001', $parcel->externalCarrierParcelNumbers->get(0));
        $this->assertSame('123', $parcel->externalCarrierParcelNumbers->get(1));
        $this->assertSame('EXT-002', $parcel->externalCarrierParcelNumbers->get(2));
    }

    public function testFromArrayMapsExternalCarrierParcelNumbersDetails(): void
    {
        $data = [
            'parcelId' => 'P006',
            'externalCarrierParcelNumbersDetails' => [
                'EXT-001' => ['carrierId' => 1, 'parcelNumber' => 'TRK-001'],
                'EXT-002' => ['carrierId' => 2, 'parcelNumber' => 'TRK-002'],
                'invalid' => 'not-an-array',
            ],
        ];

        $parcel = TrackedParcel::fromArray($data);

        $this->assertCount(2, $parcel->externalCarrierParcelNumbersDetails);
        $this->assertArrayHasKey('EXT-001', $parcel->externalCarrierParcelNumbersDetails);
        $this->assertArrayHasKey('EXT-002', $parcel->externalCarrierParcelNumbersDetails);
    }
}
