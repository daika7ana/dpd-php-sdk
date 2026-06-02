<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentParty;
use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentContentRecord;
use Daika7ana\Dpd\DTOs\Shipments\Responses\SecondaryShipment;
use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentDelivery;
use Daika7ana\Dpd\DTOs\Shipments\Responses\PrimaryShipment;
use Daika7ana\Dpd\Enums\Shipments\SecondaryShipmentType;
use PHPUnit\Framework\TestCase;

final class ShipmentResponseDtosTest extends TestCase
{
    public function testShipmentPartyMapsAllFields(): void
    {
        $data = [
            'clientId' => 100,
            'clientName' => 'Test SRL',
            'objectName' => 'Warehouse',
            'contactName' => 'John Doe',
            'address' => ['countryId' => 642, 'siteId' => 68134],
            'email' => 'test@example.com',
            'privatePerson' => false,
            'dropoffOfficeId' => 50,
            'dropoffGeoPUDOId' => 'GEO-001',
            'pickupOfficeId' => 60,
            'pickupGeoPUDOId' => 'GEO-002',
        ];

        $party = ShipmentParty::fromArray($data);

        $this->assertSame(100, $party->clientId);
        $this->assertSame('Test SRL', $party->clientName);
        $this->assertSame('Warehouse', $party->objectName);
        $this->assertSame('John Doe', $party->contactName);
        $this->assertNotNull($party->address);
        $this->assertSame('test@example.com', $party->email);
        $this->assertFalse($party->privatePerson);
        $this->assertSame(50, $party->dropoffOfficeId);
        $this->assertSame('GEO-001', $party->dropoffGeoPUDOId);
        $this->assertSame(60, $party->pickupOfficeId);
        $this->assertSame('GEO-002', $party->pickupGeoPUDOId);
    }

    public function testShipmentPartyHandlesEmptyData(): void
    {
        $party = ShipmentParty::fromArray([]);

        $this->assertNull($party->clientId);
        $this->assertNull($party->clientName);
        $this->assertNull($party->address);
    }

    public function testShipmentContentRecordMapsFields(): void
    {
        $data = [
            'parcelsCount' => 3,
            'declaredWeight' => 5.5,
            'measuredWeight' => 5.2,
            'calculationWeight' => 6.0,
            'contents' => 'Electronics',
            'package' => 'Box',
            'documents' => false,
            'palletized' => true,
            'parcels' => [
                ['parcelId' => 'P001', 'parcelNumber' => 'ABC123'],
            ],
            'pendingParcels' => false,
        ];

        $content = ShipmentContentRecord::fromArray($data);

        $this->assertSame(3, $content->parcelsCount);
        $this->assertSame(5.5, $content->declaredWeight);
        $this->assertSame(5.2, $content->measuredWeight);
        $this->assertSame(6.0, $content->calculationWeight);
        $this->assertSame('Electronics', $content->contents);
        $this->assertSame('Box', $content->package);
        $this->assertFalse($content->documents);
        $this->assertTrue($content->palletized);
        $this->assertCount(1, $content->parcels);
        $this->assertFalse($content->pendingParcels);
    }

    public function testShipmentContentRecordHandlesEmptyData(): void
    {
        $content = ShipmentContentRecord::fromArray([]);

        $this->assertNull($content->parcelsCount);
        $this->assertNull($content->declaredWeight);
        $this->assertNull($content->contents);
        $this->assertCount(0, $content->parcels);
    }

    public function testSecondaryShipmentMapsFields(): void
    {
        $data = [
            'id' => 'SEC-001',
            'type' => 'RETURN_SHIPMENT',
            'parcels' => [['parcelId' => 'P001', 'parcelNumber' => 'XYZ']],
            'pickupDate' => '2026-04-28',
            'serviceId' => 2505,
            'hasScans' => true,
        ];

        $shipment = SecondaryShipment::fromArray($data);

        $this->assertSame('SEC-001', $shipment->id);
        $this->assertSame(SecondaryShipmentType::ReturnShipment, $shipment->type);
        $this->assertCount(1, $shipment->parcels);
        $this->assertSame('2026-04-28', $shipment->pickupDate);
        $this->assertSame(2505, $shipment->serviceId);
        $this->assertTrue($shipment->hasScans);
    }

    public function testShipmentDeliveryMapsFields(): void
    {
        $data = [
            'deadline' => '2026-04-29T18:00:00',
            'deliveryDateTime' => '2026-04-29T14:30:00',
            'consignee' => 'Jane Doe',
            'deliveryNote' => 'Left at reception',
        ];

        $delivery = ShipmentDelivery::fromArray($data);

        $this->assertSame('2026-04-29T18:00:00', $delivery->deadline);
        $this->assertSame('2026-04-29T14:30:00', $delivery->deliveryDateTime);
        $this->assertSame('Jane Doe', $delivery->consignee);
        $this->assertSame('Left at reception', $delivery->deliveryNote);
    }

    public function testPrimaryShipmentMapsFields(): void
    {
        $data = ['id' => 'PRI-001', 'type' => 'RETURN_SHIPMENT'];

        $primary = PrimaryShipment::fromArray($data);

        $this->assertSame('PRI-001', $primary->id);
        $this->assertSame(SecondaryShipmentType::ReturnShipment, $primary->type);
    }

    public function testPrimaryShipmentDefaultsTypeToReturnShipment(): void
    {
        $primary = PrimaryShipment::fromArray(['id' => 'PRI-002']);

        $this->assertSame('PRI-002', $primary->id);
        $this->assertSame(SecondaryShipmentType::ReturnShipment, $primary->type);
    }
}
