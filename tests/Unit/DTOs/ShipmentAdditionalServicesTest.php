<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentAdditionalServices;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentCodAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentReturnAdditionalServices;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentObpd;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentDeclaredValueAdditionalService;
use Daika7ana\Dpd\Enums\Shipments\CodProcessingType;
use PHPUnit\Framework\TestCase;

final class ShipmentAdditionalServicesTest extends TestCase
{
    public function testShipmentCodAdditionalServiceMapsAllFields(): void
    {
        $data = [
            'amount' => 150.0,
            'currencyCode' => 'RON',
            'processingType' => 'CASH',
            'payoutToThirdParty' => false,
            'payoutToLoggedClient' => true,
            'includeShippingPrice' => true,
            'cardPaymentForbidden' => false,
            'fiscalReceiptItems' => [
                ['name' => 'Product A', 'quantity' => 2, 'vat' => 19, 'value' => 100.0],
            ],
        ];

        $cod = ShipmentCodAdditionalService::fromArray($data);

        $this->assertSame(150.0, $cod->amount);
        $this->assertSame('RON', $cod->currencyCode);
        $this->assertSame(CodProcessingType::Cash, $cod->processingType);
        $this->assertFalse($cod->payoutToThirdParty);
        $this->assertTrue($cod->payoutToLoggedClient);
        $this->assertTrue($cod->includeShippingPrice);
        $this->assertFalse($cod->cardPaymentForbidden);
        $this->assertCount(1, $cod->fiscalReceiptItems);
    }

    public function testShipmentCodAdditionalServiceHandlesMinimalData(): void
    {
        $cod = ShipmentCodAdditionalService::fromArray(['amount' => 100.0]);

        $this->assertSame(100.0, $cod->amount);
        $this->assertNull($cod->currencyCode);
        $this->assertNull($cod->processingType);
        $this->assertSame([], $cod->fiscalReceiptItems);
    }

    public function testShipmentCodAdditionalServiceToArrayRoundTrip(): void
    {
        $cod = new ShipmentCodAdditionalService(
            amount: 200.0,
            currencyCode: 'RON',
            processingType: CodProcessingType::Cash,
            payoutToThirdParty: true,
        );

        $array = $cod->toArray();

        $this->assertSame(200.0, $array['amount']);
        $this->assertSame('RON', $array['currencyCode']);
        $this->assertSame('CASH', $array['processingType']);
        $this->assertTrue($array['payoutToThirdParty']);
        $this->assertArrayNotHasKey('payoutToLoggedClient', $array);
    }

    public function testShipmentAdditionalServicesMapsAllSubServices(): void
    {
        $data = [
            'cod' => ['amount' => 100.0],
            'obpd' => ['option' => 'OPEN'],
            'declaredValue' => ['amount' => 500.0],
            'fixedTimeDelivery' => 14,
            'returns' => ['sendBackClientId' => 42],
            'specialDeliveryId' => 7,
            'deliveryToFloor' => 3,
        ];

        $services = ShipmentAdditionalServices::fromArray($data);

        $this->assertNotNull($services->cod);
        $this->assertSame(100.0, $services->cod->amount);
        $this->assertNotNull($services->obpd);
        $this->assertNotNull($services->declaredValue);
        $this->assertSame(14, $services->fixedTimeDelivery);
        $this->assertNotNull($services->returns);
        $this->assertSame(7, $services->specialDeliveryId);
        $this->assertSame(3, $services->deliveryToFloor);
    }

    public function testShipmentAdditionalServicesHandlesEmptyData(): void
    {
        $services = ShipmentAdditionalServices::fromArray([]);

        $this->assertNull($services->cod);
        $this->assertNull($services->obpd);
        $this->assertNull($services->declaredValue);
        $this->assertNull($services->fixedTimeDelivery);
        $this->assertNull($services->returns);
        $this->assertNull($services->specialDeliveryId);
        $this->assertNull($services->deliveryToFloor);
    }

    public function testShipmentAdditionalServicesToArrayOmitsNulls(): void
    {
        $services = new ShipmentAdditionalServices(
            cod: new ShipmentCodAdditionalService(amount: 50.0),
            fixedTimeDelivery: 10,
        );

        $array = $services->toArray();

        $this->assertArrayHasKey('cod', $array);
        $this->assertArrayHasKey('fixedTimeDelivery', $array);
        $this->assertArrayNotHasKey('obpd', $array);
        $this->assertArrayNotHasKey('declaredValue', $array);
        $this->assertArrayNotHasKey('returns', $array);
    }

    public function testShipmentReturnAdditionalServicesMapsSubServices(): void
    {
        $data = [
            'rod' => ['amount' => 10.0],
            'sendBackClientId' => 99,
        ];

        $returns = ShipmentReturnAdditionalServices::fromArray($data);

        $this->assertNotNull($returns->rod);
        $this->assertNull($returns->returnReceipt);
        $this->assertNull($returns->electronicReturnReceipt);
        $this->assertNull($returns->swap);
        $this->assertNull($returns->rop);
        $this->assertNull($returns->returnVoucher);
        $this->assertSame(99, $returns->sendBackClientId);
    }

    public function testShipmentReturnAdditionalServicesToArrayOmitsNulls(): void
    {
        $returns = new ShipmentReturnAdditionalServices(sendBackClientId: 42);

        $array = $returns->toArray();

        $this->assertSame(42, $array['sendBackClientId']);
        $this->assertArrayNotHasKey('rod', $array);
        $this->assertArrayNotHasKey('returnReceipt', $array);
    }

    public function testShipmentObpdFromAndToArray(): void
    {
        $data = ['option' => 'OPEN', 'returnShipmentServiceId' => 14, 'returnShipmentPayer' => 'SENDER'];

        $obpd = ShipmentObpd::fromArray($data);

        $this->assertSame(14, $obpd->returnShipmentServiceId);

        $array = $obpd->toArray();

        $this->assertSame('OPEN', $array['option']);
        $this->assertSame(14, $array['returnShipmentServiceId']);
        $this->assertSame('SENDER', $array['returnShipmentPayer']);
    }

    public function testShipmentDeclaredValueFromAndToArray(): void
    {
        $data = ['amount' => 1000.0, 'fragile' => true];

        $declaredValue = ShipmentDeclaredValueAdditionalService::fromArray($data);

        $this->assertSame(1000.0, $declaredValue->amount);
        $this->assertTrue($declaredValue->fragile);
        $this->assertNull($declaredValue->ignoreIfNotApplicable);

        $array = $declaredValue->toArray();

        $this->assertSame(1000.0, $array['amount']);
        $this->assertTrue($array['fragile']);
        $this->assertArrayNotHasKey('ignoreIfNotApplicable', $array);
    }
}
