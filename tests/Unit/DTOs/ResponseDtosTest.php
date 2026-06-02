<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Shipments\Responses\CreateShipmentResponse;
use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentPrice;
use Daika7ana\Dpd\DTOs\Tracking\Responses\TrackResponse;
use Daika7ana\Dpd\DTOs\Validation\Responses\ValidationResult;
use PHPUnit\Framework\TestCase;

final class ResponseDtosTest extends TestCase
{
    public function testCreateShipmentResponseMapsParcelsAndPrice(): void
    {
        $data = [
            'id' => 'SHIP-001',
            'parcels' => [
                ['seqNo' => 1, 'id' => 'PARCEL-001'],
                ['seqNo' => 2, 'id' => 'PARCEL-002', 'externalCarrierId' => 99],
            ],
            'price' => [
                'amount' => 10.0,
                'vat' => 1.9,
                'total' => 11.9,
                'currency' => 'RON',
                'amountLocal' => 10.0,
                'vatLocal' => 1.9,
                'totalLocal' => 11.9,
                'currencyLocal' => 'RON',
                'currencyExchangeRateUnit' => 1,
                'currencyExchangeRate' => 1.0,
            ],
            'pickupDate' => '2026-04-28',
            'deliveryDeadline' => '2026-04-29T18:00:00+03:00',
        ];

        $response = CreateShipmentResponse::fromArray($data);

        $this->assertSame('SHIP-001', $response->id);
        $this->assertCount(2, $response->parcels);
        $this->assertSame('PARCEL-001', $response->parcels->first()?->id);
        $this->assertSame(99, $response->parcels->last()?->externalCarrierId);
        $this->assertNotNull($response->price);
        $this->assertSame(11.9, $response->price->total);
        $this->assertSame('RON', $response->price->currency);
        $this->assertSame('2026-04-28', $response->pickupDate);
        $this->assertSame('2026-04-29T18:00:00+03:00', $response->deliveryDeadline);
    }

    public function testCreateShipmentResponseHandlesMissingOptionalFields(): void
    {
        $data = ['id' => 'SHIP-002', 'parcels' => []];

        $response = CreateShipmentResponse::fromArray($data);

        $this->assertSame('SHIP-002', $response->id);
        $this->assertCount(0, $response->parcels);
        $this->assertNull($response->price);
        $this->assertNull($response->pickupDate);
        $this->assertNull($response->deliveryDeadline);
    }

    public function testTrackResponseMapsParcels(): void
    {
        $data = [
            'parcels' => [
                [
                    'parcelId' => 'P001',
                    'operations' => [
                        ['operationCode' => 175, 'date' => '2026-04-28', 'description' => 'Predict'],
                    ],
                    'externalCarrierParcelNumbers' => ['EXT-001'],
                ],
            ],
        ];

        $response = TrackResponse::fromArray($data);

        $this->assertCount(1, $response->parcels);
        $this->assertSame('P001', $response->parcels->first()?->parcelId);
        $this->assertCount(1, $response->parcels->first()?->operations);
        $this->assertSame('EXT-001', $response->parcels->first()?->externalCarrierParcelNumbers->first());
    }

    public function testTrackResponseMergeCombinesParcels(): void
    {
        $response1 = TrackResponse::fromArray(['parcels' => [['parcelId' => 'P001', 'operations' => []]]]);
        $response2 = TrackResponse::fromArray(['parcels' => [['parcelId' => 'P002', 'operations' => []]]]);

        $merged = $response1->merge($response2);

        $this->assertCount(2, $merged->parcels);
        $this->assertSame('P001', $merged->parcels->first()?->parcelId);
        $this->assertSame('P002', $merged->parcels->last()?->parcelId);
    }

    public function testValidationResultMapsValidFlag(): void
    {
        $valid = ValidationResult::fromArray(['valid' => true]);
        $invalid = ValidationResult::fromArray(['valid' => false]);
        $missing = ValidationResult::fromArray([]);

        $this->assertTrue($valid->valid);
        $this->assertFalse($invalid->valid);
        $this->assertFalse($missing->valid);
    }

    public function testShipmentPriceMapsAllFields(): void
    {
        $data = [
            'amount' => 10.0,
            'vat' => 1.9,
            'total' => 11.9,
            'currency' => 'RON',
            'details' => ['NET' => ['amount' => 10.0, 'vatPercent' => 0.19]],
            'amountLocal' => 10.0,
            'vatLocal' => 1.9,
            'totalLocal' => 11.9,
            'currencyLocal' => 'RON',
            'detailsLocal' => ['NET' => ['amount' => 10.0, 'vatPercent' => 0.19]],
            'currencyExchangeRateUnit' => 1,
            'currencyExchangeRate' => 1.0,
        ];

        $price = ShipmentPrice::fromArray($data);

        $this->assertSame(10.0, $price->amount);
        $this->assertSame(1.9, $price->vat);
        $this->assertSame(11.9, $price->total);
        $this->assertSame('RON', $price->currency);
        $this->assertArrayHasKey('NET', $price->details);
        $this->assertSame(10.0, $price->details['NET']->amount);
        $this->assertSame(1.0, $price->currencyExchangeRate);
        $this->assertNull($price->returnAmounts);
    }
}
