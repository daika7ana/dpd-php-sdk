<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupRequest;
use Daika7ana\Dpd\Enums\Pickup\PickupScope;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PickupRequestValidationTest extends TestCase
{
    public function testConstructorAcceptsValidTimeFormats(): void
    {
        // Should not throw
        $request1 = new PickupRequest(visitEndTime: '9:30');
        $this->assertSame('9:30', $request1->visitEndTime);

        $request2 = new PickupRequest(visitEndTime: '11:35');
        $this->assertSame('11:35', $request2->visitEndTime);

        $request3 = new PickupRequest(visitEndTime: '23:59');
        $this->assertSame('23:59', $request3->visitEndTime);
    }

    public function testConstructorRejectsInvalidTimeFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid visitEndTime format');

        new PickupRequest(visitEndTime: 'invalid');
    }

    public function testConstructorRejectsTimeWithSeconds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PickupRequest(visitEndTime: '12:30:00');
    }

    public function testConstructorRejectsEmptyTime(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PickupRequest(visitEndTime: '');
    }

    public function testToArrayIncludesAllFields(): void
    {
        $pickupDate = new DateTimeImmutable('2026-04-28 10:00:00', new DateTimeZone('Europe/Bucharest'));
        $phone = new ShipmentPhoneNumber('+40799111111');

        $request = new PickupRequest(
            visitEndTime: '18:00',
            explicitShipmentIdList: ['SHIP-001', 'SHIP-002'],
            pickupDateTime: $pickupDate,
            autoAdjustPickupDate: true,
            pickupScope: PickupScope::ExplicitShipmentIdList,
            contactName: 'John Doe',
            phoneNumber: $phone,
        );

        $payload = $request->toArray();

        $this->assertSame('18:00', $payload['visitEndTime']);
        $this->assertSame(['SHIP-001', 'SHIP-002'], $payload['explicitShipmentIdList']);
        $this->assertNotNull($payload['pickupDateTime']);
        $this->assertTrue($payload['autoAdjustPickupDate']);
        $this->assertSame('EXPLICIT_SHIPMENT_ID_LIST', $payload['pickupScope']);
        $this->assertSame('John Doe', $payload['contactName']);
        $this->assertArrayHasKey('phoneNumber', $payload);
    }

    public function testToArrayOmitsNullOptionalFields(): void
    {
        $request = new PickupRequest(visitEndTime: '18:00');

        $payload = $request->toArray();

        $this->assertArrayNotHasKey('pickupDateTime', $payload);
        $this->assertArrayNotHasKey('autoAdjustPickupDate', $payload);
        $this->assertArrayNotHasKey('pickupScope', $payload);
        $this->assertArrayNotHasKey('contactName', $payload);
        $this->assertArrayNotHasKey('phoneNumber', $payload);
    }

    public function testToArrayKeepsPickupDateTimeOffset(): void
    {
        $pickupDate = new DateTimeImmutable('2026-04-28 15:00:00', new DateTimeZone('Europe/Bucharest'));

        $request = new PickupRequest(
            visitEndTime: '18:00',
            pickupDateTime: $pickupDate,
        );

        $payload = $request->toArray();

        // DPD documents yyyy-MM-dd'T'HH:mm:ssZ, i.e. the offset without a colon.
        $this->assertSame('2026-04-28T15:00:00+0300', $payload['pickupDateTime']);
    }
}
