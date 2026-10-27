<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CancelShipmentRequest;

final class PickupAndPaymentsSmokeTest extends SmokeTestCase
{
    public function testCanFetchPickupTermsFromLiveApi(): void
    {
        $terms = $this->client()->pickup()->terms($this->samplePickupTermsRequest());

        $this->assertGreaterThan(0, $terms->count());
    }

    public function testCanFetchPayoutsFromLiveApi(): void
    {
        $payouts = $this->client()->payments()->payouts($this->samplePayoutRequest());

        if ($this->envBool('DPD_SMOKE_EXPECT_PAYOUTS', false)) {
            $this->assertGreaterThan(0, $payouts->count());

            return;
        }

        $this->assertInstanceOf(Collection::class, $payouts);
    }

    public function testCanOrderPickupForConfiguredShipmentWhenWritesAreEnabled(): void
    {
        $this->requireWriteAccess('pickup smoke tests');

        $client = $this->client();
        $shipmentId = $this->env('DPD_SMOKE_PICKUP_SHIPMENT_ID');
        $createdShipmentId = null;

        if ($shipmentId === null) {
            $createdShipmentId = $client->shipments()->create($this->sampleShipmentRequest())->id;
            $shipmentId = $createdShipmentId;
        }

        try {
            $orders = $client->pickup()->order($this->samplePickupRequest($shipmentId));

            $this->assertGreaterThan(0, $orders->count());
            $this->assertSame($shipmentId, $orders->first()?->shipmentIds->first());
        } finally {
            if ($createdShipmentId !== null) {
                $client->shipments()->cancel(new CancelShipmentRequest($createdShipmentId, 'Smoke test cleanup'));
            }
        }
    }
}
