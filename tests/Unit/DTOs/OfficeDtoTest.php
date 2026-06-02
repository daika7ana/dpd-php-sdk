<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Locations\Offices\Office;
use Daika7ana\Dpd\DTOs\Locations\Offices\OfficeWorkingTimeSchedule;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Enums\Services\CargoType;
use PHPUnit\Framework\TestCase;

final class OfficeDtoTest extends TestCase
{
    public function testFromArrayMapsAllRequiredFields(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Central Office',
            'nameEn' => 'Central Office EN',
            'siteId' => 68134,
            'type' => 'OFFICE',
            'palletOffice' => true,
            'cardPaymentAllowed' => true,
            'cashPaymentAllowed' => false,
            'pickUpAllowed' => true,
            'dropOffAllowed' => false,
            'cargoTypesAllowed' => ['PARCEL', 'PALLET'],
            'workingTimeSchedule' => [
                ['dayOfWeek' => 1, 'workingTimeFrom' => '09:00', 'workingTimeTo' => '17:00'],
                ['dayOfWeek' => 2, 'workingTimeFrom' => '09:00', 'workingTimeTo' => '17:00'],
            ],
        ];

        $office = Office::fromArray($data);

        $this->assertSame(123, $office->id);
        $this->assertSame('Central Office', $office->name->local);
        $this->assertSame('Central Office EN', $office->name->english);
        $this->assertSame(68134, $office->siteId);
        $this->assertSame(OfficeType::Office, $office->type);
        $this->assertTrue($office->palletOffice);
        $this->assertTrue($office->cardPaymentAllowed);
        $this->assertFalse($office->cashPaymentAllowed);
        $this->assertTrue($office->pickUpAllowed);
        $this->assertFalse($office->dropOffAllowed);
        $this->assertCount(2, $office->cargoTypesAllowed);
        $this->assertSame(CargoType::Parcel, $office->cargoTypesAllowed[0]);
        $this->assertSame(CargoType::Pallet, $office->cargoTypesAllowed[1]);
        $this->assertCount(2, $office->workingTimeSchedule);
        $this->assertInstanceOf(OfficeWorkingTimeSchedule::class, $office->workingTimeSchedule[0]);
    }

    public function testFromArrayMapsOptionalFields(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Test',
            'siteId' => 100,
            'type' => 'APT',
            'workingTimeFrom' => '08:00',
            'workingTimeTo' => '20:00',
            'workingTimeHalfFrom' => '08:00',
            'workingTimeHalfTo' => '12:00',
            'workingTimeDayOffFrom' => '10:00',
            'workingTimeDayOffTo' => '14:00',
            'sameDayDepartureCutoff' => '15:00',
            'sameDayDepartureCutoffHalf' => '11:00',
            'sameDayDepartureCutoffDayOff' => '13:00',
            'maxParcelWeight' => 30.5,
            'nearbyOfficeId' => 789,
            'validFrom' => '2026-01-01',
            'validTo' => '2026-12-31',
            'address' => ['countryId' => 642, 'siteId' => 100, 'streetName' => 'Main'],
            'maxParcelDimensions' => ['width' => 10, 'depth' => 20, 'height' => 30],
            'routingInformation' => ['officeId' => 12, 'hubId' => 5],
        ];

        $office = Office::fromArray($data);

        $this->assertSame(OfficeType::Apt, $office->type);
        $this->assertSame('08:00', $office->workingTimeFrom);
        $this->assertSame('20:00', $office->workingTimeTo);
        $this->assertSame('08:00', $office->workingTimeHalfFrom);
        $this->assertSame('12:00', $office->workingTimeHalfTo);
        $this->assertSame('10:00', $office->workingTimeDayOffFrom);
        $this->assertSame('14:00', $office->workingTimeDayOffTo);
        $this->assertSame('15:00', $office->sameDayDepartureCutoff);
        $this->assertSame('11:00', $office->sameDayDepartureCutoffHalf);
        $this->assertSame('13:00', $office->sameDayDepartureCutoffDayOff);
        $this->assertSame(30.5, $office->maxParcelWeight);
        $this->assertSame(789, $office->nearbyOfficeId);
        $this->assertSame('2026-01-01', $office->validFrom);
        $this->assertSame('2026-12-31', $office->validTo);
        $this->assertNotNull($office->address);
        $this->assertNotNull($office->maxParcelDimensions);
        $this->assertNotNull($office->routingInformation);
    }

    public function testFromArrayDefaultsForMissingOptionalFields(): void
    {
        $data = ['id' => 1, 'name' => 'Test', 'siteId' => 100, 'type' => 'OFFICE'];

        $office = Office::fromArray($data);

        $this->assertFalse($office->palletOffice);
        $this->assertFalse($office->cardPaymentAllowed);
        $this->assertFalse($office->cashPaymentAllowed);
        $this->assertFalse($office->pickUpAllowed);
        $this->assertFalse($office->dropOffAllowed);
        $this->assertEmpty($office->cargoTypesAllowed);
        $this->assertEmpty($office->workingTimeSchedule);
        $this->assertNull($office->address);
        $this->assertNull($office->workingTimeFrom);
        $this->assertNull($office->maxParcelWeight);
        $this->assertNull($office->routingInformation);
    }

    public function testFromArrayHandlesNonArrayWorkingTimeScheduleEntries(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Test',
            'siteId' => 100,
            'type' => 'OFFICE',
            'workingTimeSchedule' => [
                ['dayOfWeek' => 1, 'workingTimeFrom' => '09:00', 'workingTimeTo' => '17:00'],
                'invalid-entry',
                null,
            ],
        ];

        $office = Office::fromArray($data);

        $this->assertCount(1, $office->workingTimeSchedule);
    }
}
