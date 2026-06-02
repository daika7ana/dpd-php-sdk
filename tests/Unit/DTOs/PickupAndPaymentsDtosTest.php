<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Pickup\Responses\PickupOrder;
use Daika7ana\Dpd\DTOs\Payments\Payouts\Payout;
use Daika7ana\Dpd\DTOs\Payments\Payouts\PayoutDetail;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentParcel;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\GoodsItem;
use Daika7ana\Dpd\Enums\Payments\PayoutDocumentType;
use Daika7ana\Dpd\Enums\Payments\PayoutPaymentType;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

final class PickupAndPaymentsDtosTest extends TestCase
{
    public function testPickupOrderMapsFields(): void
    {
        $data = [
            'id' => 500,
            'shipmentIds' => ['SHIP-001', 'SHIP-002', 123],
            'pickupPeriodFrom' => '09:00',
            'pickupPeriodTo' => '12:00',
        ];

        $order = PickupOrder::fromArray($data);

        $this->assertSame(500, $order->id);
        $this->assertCount(3, $order->shipmentIds);
        $this->assertSame('SHIP-001', $order->shipmentIds->first());
        $this->assertSame('123', $order->shipmentIds->last());
        $this->assertSame('09:00', $order->pickupPeriodFrom);
        $this->assertSame('12:00', $order->pickupPeriodTo);
    }

    public function testPickupOrderHandlesMinimalData(): void
    {
        $order = PickupOrder::fromArray([]);

        $this->assertSame(0, $order->id);
        $this->assertCount(0, $order->shipmentIds);
        $this->assertNull($order->pickupPeriodFrom);
        $this->assertNull($order->pickupPeriodTo);
    }

    public function testPickupOrderFiltersNonScalarShipmentIds(): void
    {
        $data = [
            'id' => 501,
            'shipmentIds' => ['SHIP-001', null, ['invalid'], 'SHIP-002'],
        ];

        $order = PickupOrder::fromArray($data);

        $this->assertCount(2, $order->shipmentIds);
    }

    public function testPayoutMapsAllFields(): void
    {
        $data = [
            'date' => '2026-04-28',
            'docId' => 1001,
            'docType' => 'CASH',
            'paymentType' => 'CASH',
            'payee' => 'Test SRL',
            'currency' => 'RON',
            'amount' => 500.0,
            'details' => [
                [
                    'lineNo' => 1,
                    'shipmentId' => 'SHIP-001',
                    'pickupDate' => '2026-04-25',
                    'deliveryDate' => '2026-04-27',
                    'sender' => 'Sender SRL',
                    'recipient' => 'Recipient SRL',
                    'amount' => 50.0,
                    'currency' => 'RON',
                ],
                [
                    'lineNo' => 2,
                    'shipmentId' => 'SHIP-002',
                    'amount' => 75.0,
                ],
            ],
        ];

        $payout = Payout::fromArray($data);

        $this->assertSame('2026-04-28', $payout->date);
        $this->assertSame(1001, $payout->docId);
        $this->assertSame(PayoutDocumentType::Cash, $payout->docType);
        $this->assertSame(PayoutPaymentType::Cash, $payout->paymentType);
        $this->assertSame('Test SRL', $payout->payee);
        $this->assertSame('RON', $payout->currency);
        $this->assertSame(500.0, $payout->amount);
        $this->assertCount(2, $payout->details);
        $this->assertInstanceOf(PayoutDetail::class, $payout->details->first());
    }

    public function testPayoutHandlesMinimalData(): void
    {
        $payout = Payout::fromArray([]);

        $this->assertSame('', $payout->date);
        $this->assertSame(0, $payout->docId);
        $this->assertSame(PayoutDocumentType::Cash, $payout->docType);
        $this->assertSame(PayoutPaymentType::Cash, $payout->paymentType);
        $this->assertSame('', $payout->payee);
        $this->assertSame('', $payout->currency);
        $this->assertSame(0.0, $payout->amount);
        $this->assertCount(0, $payout->details);
    }

    public function testPayoutDetailMapsAllFields(): void
    {
        $data = [
            'lineNo' => 1,
            'shipmentId' => 'SHIP-001',
            'pickupDate' => '2026-04-25',
            'primaryShipmentPickupDate' => '2026-04-24',
            'deliveryDate' => '2026-04-27',
            'sender' => 'Sender SRL',
            'recipient' => 'Recipient SRL',
            'note' => 'COD payment',
            'ref1' => 'REF-001',
            'ref2' => 'REF-002',
            'currency' => 'RON',
            'order' => 5,
            'amount' => 150.0,
        ];

        $detail = PayoutDetail::fromArray($data);

        $this->assertSame(1, $detail->lineNo);
        $this->assertSame('SHIP-001', $detail->shipmentId);
        $this->assertSame('2026-04-25', $detail->pickupDate);
        $this->assertSame('2026-04-24', $detail->primaryShipmentPickupDate);
        $this->assertSame('2026-04-27', $detail->deliveryDate);
        $this->assertSame('Sender SRL', $detail->sender);
        $this->assertSame('Recipient SRL', $detail->recipient);
        $this->assertSame('COD payment', $detail->note);
        $this->assertSame('REF-001', $detail->ref1);
        $this->assertSame('REF-002', $detail->ref2);
        $this->assertSame('RON', $detail->currency);
        $this->assertSame(5, $detail->order);
        $this->assertSame(150.0, $detail->amount);
    }

    public function testPayoutDetailHandlesMinimalData(): void
    {
        $detail = PayoutDetail::fromArray([]);

        $this->assertSame(0, $detail->lineNo);
        $this->assertSame('', $detail->shipmentId);
        $this->assertNull($detail->pickupDate);
        $this->assertNull($detail->amount);
    }

    public function testShipmentServiceFromAndToArray(): void
    {
        $pickupDate = new DateTimeImmutable('2026-04-28');

        $service = new ShipmentService(
            serviceId: 2505,
            pickupDate: $pickupDate,
            autoAdjustPickupDate: true,
            deferredDays: 3,
            saturdayDelivery: false,
        );

        $array = $service->toArray();

        $this->assertSame(2505, $array['serviceId']);
        $this->assertSame('2026-04-28', $array['pickupDate']);
        $this->assertTrue($array['autoAdjustPickupDate']);
        $this->assertSame(3, $array['deferredDays']);
        $this->assertFalse($array['saturdayDelivery']);
        $this->assertArrayNotHasKey('additionalServices', $array);
    }

    public function testShipmentServiceFromArrayFallsBackToId(): void
    {
        $data = ['id' => 9999];

        $service = ShipmentService::fromArray($data);

        $this->assertSame(9999, $service->serviceId);
    }

    public function testShipmentServiceFromArrayMapsAdditionalServices(): void
    {
        $data = [
            'serviceId' => 2505,
            'additionalServices' => [
                'cod' => ['amount' => 100.0],
                'fixedTimeDelivery' => 14,
            ],
        ];

        $service = ShipmentService::fromArray($data);

        $this->assertNotNull($service->additionalServices);
        $this->assertNotNull($service->additionalServices->cod);
        $this->assertSame(100.0, $service->additionalServices->cod->amount);
        $this->assertSame(14, $service->additionalServices->fixedTimeDelivery);
    }

    public function testShipmentParcelToArrayRoundTrip(): void
    {
        $parcel = new ShipmentParcel(
            seqNo: 1,
            weight: 2.5,
        );

        $array = $parcel->toArray();

        $this->assertSame(1, $array['seqNo']);
        $this->assertSame(2.5, $array['weight']);
        $this->assertArrayNotHasKey('ref1', $array);
    }

    public function testShipmentContentToArrayRoundTrip(): void
    {
        $content = new ShipmentContent(
            contents: 'Electronics',
            package: 'Box',
            parcelsCount: 2,
            totalWeight: 5.0,
        );

        $array = $content->toArray();

        $this->assertSame(2, $array['parcelsCount']);
        $this->assertSame(5.0, $array['totalWeight']);
        $this->assertSame('Electronics', $array['contents']);
        $this->assertSame('Box', $array['package']);
    }

    public function testGoodsItemToArrayRoundTrip(): void
    {
        $item = new GoodsItem(
            description: 'Laptop',
            hsCode: '8471.30',
            quantity: 1,
            valuePerItem: 500.0,
            weightPerItem: 2.0,
            originCountryCode: 'RO',
        );

        $array = $item->toArray();

        $this->assertSame('Laptop', $array['description']);
        $this->assertSame('8471.30', $array['hsCode']);
        $this->assertSame(1, $array['quantity']);
        $this->assertSame(500.0, $array['valuePerItem']);
        $this->assertSame(2.0, $array['weightPerItem']);
        $this->assertSame('RO', $array['originCountryCode']);
    }
}
