<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Services\Catalog\CourierService;
use Daika7ana\Dpd\DTOs\Services\AdditionalServices\AdditionalCourierServices;
use Daika7ana\Dpd\DTOs\Services\AdditionalServices\AdditionalCourierService;
use Daika7ana\Dpd\DTOs\Calculations\Results\CalculationResult;
use Daika7ana\Dpd\Enums\Services\AdditionalServiceAllowance;
use Daika7ana\Dpd\Enums\Services\CargoType;
use PHPUnit\Framework\TestCase;

final class ServicesAndCalculationsDtosTest extends TestCase
{
    public function testCourierServiceMapsAllFields(): void
    {
        $data = [
            'id' => 2505,
            'name' => 'DPD Classic',
            'nameEn' => 'DPD Classic',
            'cargoType' => 'PARCEL',
            'requireParcelWeight' => true,
            'requireParcelSize' => false,
            'additionalServices' => [
                'cod' => ['allowance' => 'ALLOWED'],
                'obpd' => ['allowance' => 'FORBIDDEN'],
            ],
        ];

        $service = CourierService::fromArray($data);

        $this->assertSame(2505, $service->id);
        $this->assertSame('DPD Classic', $service->name);
        $this->assertSame('DPD Classic', $service->nameEn);
        $this->assertSame(CargoType::Parcel, $service->cargoType);
        $this->assertTrue($service->requireParcelWeight);
        $this->assertFalse($service->requireParcelSize);
        $this->assertNotNull($service->additionalServices->cod);
        $this->assertSame(AdditionalServiceAllowance::Allowed, $service->additionalServices->cod->allowance);
        $this->assertNotNull($service->additionalServices->obpd);
        $this->assertSame(AdditionalServiceAllowance::Forbidden, $service->additionalServices->obpd->allowance);
    }

    public function testCourierServiceDefaults(): void
    {
        $service = CourierService::fromArray([]);

        $this->assertSame(0, $service->id);
        $this->assertSame('', $service->name);
        $this->assertSame('', $service->nameEn);
        $this->assertSame(CargoType::Parcel, $service->cargoType);
        $this->assertFalse($service->requireParcelWeight);
        $this->assertFalse($service->requireParcelSize);
    }

    public function testAdditionalCourierServicesMapsAllSubServices(): void
    {
        $data = [
            'cod' => ['allowance' => 'ALLOWED'],
            'obpd' => ['allowance' => 'FORBIDDEN'],
            'declaredValue' => ['allowance' => 'ALLOWED'],
            'fixedTimeDelivery' => ['allowance' => 'FORBIDDEN'],
            'specialDelivery' => ['allowance' => 'ALLOWED'],
            'deliveryToFloor' => ['allowance' => 'FORBIDDEN'],
            'rod' => ['allowance' => 'ALLOWED'],
            'returnReceipt' => ['allowance' => 'FORBIDDEN'],
            'swap' => ['allowance' => 'ALLOWED'],
            'rop' => ['allowance' => 'FORBIDDEN'],
            'returnVoucher' => ['allowance' => 'ALLOWED'],
        ];

        $services = AdditionalCourierServices::fromArray($data);

        $this->assertNotNull($services->cod);
        $this->assertSame(AdditionalServiceAllowance::Allowed, $services->cod->allowance);
        $this->assertNotNull($services->obpd);
        $this->assertSame(AdditionalServiceAllowance::Forbidden, $services->obpd->allowance);
        $this->assertNotNull($services->declaredValue);
        $this->assertNotNull($services->fixedTimeDelivery);
        $this->assertNotNull($services->specialDelivery);
        $this->assertNotNull($services->deliveryToFloor);
        $this->assertNotNull($services->rod);
        $this->assertNotNull($services->returnReceipt);
        $this->assertNotNull($services->swap);
        $this->assertNotNull($services->rop);
        $this->assertNotNull($services->returnVoucher);
    }

    public function testAdditionalCourierServicesHandlesNonArrayValues(): void
    {
        $data = [
            'cod' => ['allowance' => 'ALLOWED'],
            'obpd' => 'invalid',
            'declaredValue' => null,
        ];

        $services = AdditionalCourierServices::fromArray($data);

        $this->assertNotNull($services->cod);
        $this->assertNull($services->obpd);
        $this->assertNull($services->declaredValue);
    }

    public function testAdditionalCourierServiceFromAndToArray(): void
    {
        $service = AdditionalCourierService::fromArray(['allowance' => 'ALLOWED']);

        $this->assertSame(AdditionalServiceAllowance::Allowed, $service->allowance);

        $serviceForbidden = AdditionalCourierService::fromArray(['allowance' => 'FORBIDDEN']);

        $this->assertSame(AdditionalServiceAllowance::Forbidden, $serviceForbidden->allowance);
    }

    public function testCalculationResultMapsFieldsWithPrice(): void
    {
        $data = [
            'serviceId' => 2505,
            'price' => [
                'amount' => 15.0,
                'vat' => 2.85,
                'total' => 17.85,
                'currency' => 'RON',
                'amountLocal' => 15.0,
                'vatLocal' => 2.85,
                'totalLocal' => 17.85,
                'currencyLocal' => 'RON',
                'currencyExchangeRateUnit' => 1,
                'currencyExchangeRate' => 1.0,
            ],
            'pickupDate' => '2026-04-28',
            'deliveryDeadline' => '2026-04-29',
        ];

        $result = CalculationResult::fromArray($data);

        $this->assertSame(2505, $result->serviceId);
        $this->assertNotNull($result->price);
        $this->assertSame(17.85, $result->price->total);
        $this->assertSame('2026-04-28', $result->pickupDate);
        $this->assertSame('2026-04-29', $result->deliveryDeadline);
        $this->assertNull($result->error);
        $this->assertNull($result->additionalServices);
    }

    public function testCalculationResultMapsFieldsWithError(): void
    {
        $data = [
            'serviceId' => 2505,
            'error' => ['code' => 300, 'message' => 'Invalid parcel weight', 'id' => 'err-300'],
        ];

        $result = CalculationResult::fromArray($data);

        $this->assertSame(2505, $result->serviceId);
        $this->assertNull($result->price);
        $this->assertNotNull($result->error);
        $this->assertSame(300, $result->error->code);
    }

    public function testCalculationResultHandlesEmptyData(): void
    {
        $result = CalculationResult::fromArray([]);

        $this->assertSame(0, $result->serviceId);
        $this->assertNull($result->price);
        $this->assertNull($result->additionalServices);
        $this->assertNull($result->pickupDate);
        $this->assertNull($result->deliveryDeadline);
        $this->assertNull($result->error);
    }
}
