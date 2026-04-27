<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Common\Addressing\BankAccount;
use Daika7ana\Dpd\DTOs\Common\Documents\LabelInfo;
use Daika7ana\Dpd\DTOs\Common\Routing\RoutingInformation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentAddress;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelSize;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Common\Support\ShipmentPriceAmount;
use Daika7ana\Dpd\DTOs\Locations\Geography\Country;
use Daika7ana\Dpd\DTOs\Locations\Geography\Site;
use PHPUnit\Framework\TestCase;

final class PrimitiveDtosTest extends TestCase
{
    public function testAddressLocationMapsCoordinateAwarePayloads(): void
    {
        $location = AddressLocation::fromArray([
            'countryId' => 642,
            'siteId' => 12345,
            'siteName' => 'Bucuresti',
            'postCode' => '010101',
            'x' => 26.1,
            'y' => 44.4,
        ]);

        $this->assertSame(642, $location->countryId);
        $this->assertSame(12345, $location->siteId);
        $this->assertTrue($location->hasCoordinates());
    }

    public function testShipmentAddressKeepsLocalAndForeignShapesExplicit(): void
    {
        $localAddress = ShipmentAddress::fromArray([
            'countryId' => 642,
            'siteId' => 12345,
            'streetType' => 'Str.',
            'streetName' => 'Libertatii',
            'streetNo' => '10',
            'blockNo' => 'A1',
        ]);
        $foreignAddress = ShipmentAddress::fromArray([
            'countryId' => 276,
            'stateId' => 'BE',
            'siteName' => 'Berlin',
            'addressLine1' => 'Alexanderplatz 1',
            'addressLine2' => 'Floor 2',
        ]);

        $this->assertTrue($localAddress->usesLocalAddressing());
        $this->assertFalse($localAddress->usesForeignAddressing());
        $this->assertTrue($foreignAddress->usesForeignAddressing());
        $this->assertFalse($foreignAddress->usesLocalAddressing());
    }

    public function testReferenceDtosPreserveBilingualFieldsAndMissingTranslations(): void
    {
        $country = Country::fromCsvRow([
            'id' => '642',
            'name' => 'Romania',
            'isoAlpha2' => 'RO',
            'isoAlpha3' => 'ROU',
            'postCodeFormats' => '######',
            'requireState' => '0',
            'addressType' => '1',
            'streetTypes' => 'Str.,Bd.',
            'streetTypesEn' => 'Street',
            'complexTypes' => 'Bl.',
            'complexTypesEn' => 'Block',
            'siteNomen' => '1',
        ]);
        $site = Site::fromArray([
            'id' => 68134,
            'countryId' => 642,
            'type' => 'Municipiu',
            'name' => 'Bucuresti',
            'municipality' => 'Bucuresti',
            'region' => 'Bucuresti',
            'postCode' => '010101',
            'addressNomenclature' => 1,
        ]);

        $this->assertSame('Romania', $country->name->local);
        $this->assertSame('', $country->name->english);
        $this->assertCount(2, $country->streetTypes);
        $this->assertSame('Street', $country->streetTypes[0]->name->english);
        $this->assertSame('', $country->streetTypes[1]->name->english);
        $this->assertSame('Municipiu', $site->type->local);
        $this->assertSame('', $site->type->english);
        $this->assertSame('', $site->municipality->english);
    }

    public function testPrimitiveDtosMapSharedFields(): void
    {
        $phoneNumber = ShipmentPhoneNumber::fromArray(['number' => '+40799123456', 'extension' => '12']);
        $parcelRef = ShipmentParcelRef::fromArray(['id' => 'PARCEL123']);
        $parcelSize = ShipmentParcelSize::fromArray(['width' => 10, 'depth' => 20, 'height' => 30]);
        $priceAmount = ShipmentPriceAmount::fromArray(['amount' => 12.5, 'percent' => 0.1, 'vatPercent' => 0.19]);
        $bankAccount = BankAccount::fromArray(['iban' => 'RO49AAAA1B31007593840000', 'accountHolder' => 'Example SRL']);
        $routingInformation = RoutingInformation::fromArray([
            'officeId' => 12,
            'hubId' => 5,
            'priority' => 2,
            'tour' => ['number' => 7, 'officeId' => 12, 'ramp' => 'R1', 'cell' => 'C3'],
        ]);
        $labelInfo = LabelInfo::fromArray([
            'parcelId' => 'PARCEL123',
            'hubId' => 5,
            'officeId' => 12,
            'officeName' => 'Central Office',
            'deadlineDay' => 27,
            'deadlineMonth' => 4,
            'tourId' => 8,
            'fullBarcode' => '123456789012345678',
            'exportPriority' => 1,
        ]);

        $this->assertSame('+40799123456', $phoneNumber->number);
        $this->assertTrue($parcelRef->hasAnyReference());
        $this->assertSame(20, $parcelSize->depth);
        $this->assertSame(0.19, $priceAmount->vatPercent);
        $this->assertSame('Example SRL', $bankAccount->accountHolder);
        $this->assertSame('R1', $routingInformation->tour?->ramp);
        $this->assertSame('Central Office', $labelInfo->officeName);
    }
}
