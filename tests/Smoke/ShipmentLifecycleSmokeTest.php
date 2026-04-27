<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CancelShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\ShipmentInformationRequest;

final class ShipmentLifecycleSmokeTest extends SmokeTestCase
{
    public function testCanCreateInspectAndOptionallyCancelShipment(): void
    {
        $this->requireWriteAccess('shipment lifecycle smoke tests');

        $client = $this->client();
        $shipment = $client->shipments()->create($this->sampleShipmentRequest());

        $this->assertNotSame('', $shipment->id);
        $this->assertGreaterThan(0, $shipment->parcels->count());

        $info = $client->shipments()->info(new ShipmentInformationRequest([$shipment->id]));

        $this->assertGreaterThan(0, $info->shipments->count());

        $parcelId = $shipment->parcels->first()?->id;
        $this->assertNotNull($parcelId);

        $barcodeInformation = $client->shipments()->barcodeInformation(new ShipmentParcelRef($parcelId, null, null));

        $this->assertTrue(
            $barcodeInformation->labelInfo !== null
            || $barcodeInformation->primaryParcelId !== null
            || $barcodeInformation->initialParcelId !== null,
        );

        if ($this->envBool('DPD_SMOKE_CANCEL_CREATED_SHIPMENTS', false)) {
            $client->shipments()->cancel(new CancelShipmentRequest($shipment->id, 'Smoke test cleanup'));
            $this->addToAssertionCount(1);
        }
    }
}
