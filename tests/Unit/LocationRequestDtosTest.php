<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Locations\Requests\NearestOfficesRequest;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\Enums\Locations\OfficeFeature;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use PHPUnit\Framework\TestCase;

final class LocationRequestDtosTest extends TestCase
{
    public function testNearestOfficesRequestSerializesTypedFilters(): void
    {
        $request = new NearestOfficesRequest(
            address: new LocalShipmentAddress(
                location: AddressLocation::fromArray([
                    'countryId' => 642,
                    'siteId' => 68134,
                    'siteName' => 'Bucuresti',
                    'postCode' => '010101',
                    'x' => 26.1025,
                    'y' => 44.4268,
                ]),
                streetType: 'Str.',
                streetName: 'Victoriei',
                streetNo: '10',
            ),
            distance: 1200,
            limit: 5,
            officeType: OfficeType::Apt,
            officeFeatures: [OfficeFeature::CardPayment, OfficeFeature::DropOff],
        );

        $this->assertSame([
            'address' => [
                'countryId' => 642,
                'siteId' => 68134,
                'siteName' => 'Bucuresti',
                'postCode' => '010101',
                'x' => 26.1025,
                'y' => 44.4268,
                'streetType' => 'Str.',
                'streetName' => 'Victoriei',
                'streetNo' => '10',
            ],
            'distance' => 1200,
            'limit' => 5,
            'officeType' => 'APT',
            'officeFeatures' => ['CARD_PAYMENT', 'DROP_OFF'],
        ], $request->toArray());
    }
}
