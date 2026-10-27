<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Cache\ArrayReferenceDataCache;
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Locations\Requests\BlockSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\ComplexSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\CountrySearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\NearestOfficesRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\OfficeSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\PointOfInterestSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\SiteSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\StateSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\StreetSearchRequest;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\Enums\Locations\AddressType;
use Daika7ana\Dpd\Enums\Locations\NomenclatureSupport;
use Daika7ana\Dpd\Enums\Locations\OfficeFeature;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Enums\Services\CargoType;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use Daika7ana\Dpd\Tests\Support\TestClientFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class LocationsResourceTest extends TestCase
{
    public function testLookupEndpointsHydrateTypedLocationDtos(): void
    {
        $client = $this->makeClient(static function (RequestInterface $request): Response {
            return match ($request->getUri()->getPath()) {
                '/v1/location/country/642' => self::jsonResponse([
                    'country' => [
                        'id' => 642,
                        'name' => 'Romania',
                        'nameEn' => 'Romania',
                        'isoAlpha2' => 'RO',
                        'isoAlpha3' => 'ROU',
                        'postCodeFormats' => ['NNNNNN', ''],
                        'requireState' => false,
                        'addressType' => 1,
                        'currencyCode' => 'RON',
                        'defaultOfficeId' => 100,
                        'streetTypes' => [['name' => 'Str.', 'nameEn' => 'Street']],
                        'complexTypes' => [['name' => 'Bl.', 'nameEn' => 'Block']],
                        'siteNomen' => 1,
                    ],
                ]),
                '/v1/location/state/B' => self::jsonResponse([
                    'state' => [
                        'id' => 'B',
                        'name' => 'Bucuresti',
                        'nameEn' => 'Bucharest',
                        'stateAlpha' => 'B',
                        'countryId' => 642,
                    ],
                ]),
                '/v1/location/site/68134' => self::jsonResponse([
                    'site' => [
                        'id' => 68134,
                        'countryId' => 642,
                        'mainSiteId' => null,
                        'type' => 'Municipiu',
                        'typeEn' => 'Municipality',
                        'name' => 'Bucuresti',
                        'nameEn' => 'Bucharest',
                        'municipality' => 'Bucuresti',
                        'municipalityEn' => 'Bucharest',
                        'region' => 'Bucuresti',
                        'regionEn' => 'Bucharest',
                        'postCode' => '010101',
                        'addressNomenclature' => 2,
                        'x' => 26.1025,
                        'y' => 44.4268,
                        'servingDays' => '1111100',
                        'servingOfficeId' => 200,
                        'servingHubOfficeId' => 300,
                    ],
                ]),
                '/v1/location/street/7001' => self::jsonResponse([
                    'street' => [
                        'id' => 7001,
                        'siteId' => 68134,
                        'type' => 'Str.',
                        'typeEn' => 'Street',
                        'name' => 'Victoriei',
                        'nameEn' => 'Victory',
                        'actualId' => 7002,
                        'actualType' => 'Bd.',
                        'actualTypeEn' => 'Boulevard',
                        'actualName' => 'Unirii',
                        'actualNameEn' => 'Union',
                    ],
                ]),
                '/v1/location/complex/71' => self::jsonResponse([
                    'complex' => [
                        'id' => 71,
                        'siteId' => 68134,
                        'type' => 'Bl.',
                        'typeEn' => 'Block',
                        'name' => 'Central',
                        'nameEn' => 'Central',
                    ],
                ]),
                '/v1/location/poi/990' => self::jsonResponse([
                    'poi' => [
                        'id' => 990,
                        'siteId' => 68134,
                        'name' => 'Piata Romana',
                        'nameEn' => 'Roman Square',
                        'type' => 'Piata',
                        'typeEn' => 'Square',
                        'address' => 'Sector 1',
                        'addressEn' => 'District 1',
                        'x' => 26.097,
                        'y' => 44.447,
                    ],
                ]),
                '/v1/location/office/100' => self::jsonResponse([
                    'office' => [
                        'id' => 100,
                        'name' => 'Oficiu Central',
                        'nameEn' => 'Central Office',
                        'siteId' => 68134,
                        'address' => [
                            'countryId' => 642,
                            'siteId' => 68134,
                            'siteName' => 'Bucuresti',
                            'postCode' => '010101',
                            'streetType' => 'Str.',
                            'streetName' => 'Victoriei',
                            'streetNo' => '10',
                            'fullAddressString' => 'Str. Victoriei 10, Bucuresti',
                            'siteAddressString' => 'Bucuresti 010101',
                            'localAddressString' => 'Str. Victoriei 10',
                        ],
                        'workingTimeFrom' => '09:00',
                        'workingTimeTo' => '18:00',
                        'sameDayDepartureCutoff' => '16:30',
                        'maxParcelDimensions' => ['width' => 120, 'depth' => 80, 'height' => 60],
                        'maxParcelWeight' => 31.5,
                        'type' => 'OFFICE',
                        'nearbyOfficeId' => 101,
                        'workingTimeSchedule' => [
                            ['date' => '2026-04-28', 'workingTimeFrom' => '09:00', 'workingTimeTo' => '18:00', 'sameDayDepartureCutoff' => '16:30', 'standardSchedule' => true],
                        ],
                        'palletOffice' => false,
                        'cardPaymentAllowed' => true,
                        'cashPaymentAllowed' => true,
                        'validFrom' => '2026-01-01',
                        'validTo' => '2026-12-31',
                        'cargoTypesAllowed' => ['PARCEL', 'PALLET'],
                        'pickUpAllowed' => true,
                        'dropOffAllowed' => true,
                        'routingInformation' => [
                            'officeId' => 100,
                            'hubId' => 2,
                            'priority' => 7,
                            'tour' => ['number' => 12, 'officeId' => 100, 'ramp' => 'R1', 'cell' => 'C3'],
                        ],
                    ],
                ]),
                default => new Response(404, ['Content-Type' => 'application/json'], '{}'),
            };
        });

        $country = $client->locations()->getCountry(642);
        $state = $client->locations()->getState('B');
        $site = $client->locations()->getSite(68134);
        $street = $client->locations()->getStreet(7001);
        $complex = $client->locations()->getComplex(71);
        $poi = $client->locations()->getPointOfInterest(990);
        $office = $client->locations()->getOffice(100);

        $this->assertNotNull($country);
        $this->assertSame(AddressType::Local, $country->addressType);
        $this->assertSame(NomenclatureSupport::Full, $country->siteNomenclature);
        $this->assertSame('', $country->postCodeFormats[1]);
        $this->assertSame('Street', $country->streetTypes[0]->name->english);

        $this->assertNotNull($state);
        $this->assertSame('Bucharest', $state->name->english);

        $this->assertNotNull($site);
        $this->assertSame(NomenclatureSupport::Partial, $site->addressNomenclature);
        $this->assertSame('1111100', $site->servingDays);

        $this->assertNotNull($street);
        $this->assertSame('Union', $street->actualName?->english);

        $this->assertNotNull($complex);
        $this->assertSame('Central', $complex->name->english);

        $this->assertNotNull($poi);
        $this->assertSame('Square', $poi->type->english);

        $this->assertNotNull($office);
        $this->assertSame('Central Office', $office->name->english);
        $this->assertSame('Str. Victoriei 10, Bucuresti', $office->address?->fullAddressString);
        $this->assertSame(CargoType::Parcel, $office->cargoTypesAllowed[0]);
        $this->assertCount(1, $office->workingTimeSchedule);
        $this->assertSame('R1', $office->routingInformation?->tour?->ramp);
    }

    public function testSearchEndpointsUseExpectedPayloadsAndMapCollections(): void
    {
        $httpClient = new FakeHttpClient(static function (RequestInterface $request): Response {
            return match ($request->getUri()->getPath()) {
                '/v1/location/country' => self::jsonResponse(['countries' => [['id' => 642, 'name' => 'Romania', 'nameEn' => 'Romania', 'isoAlpha2' => 'RO', 'isoAlpha3' => 'ROU', 'postCodeFormats' => ['NNNNNN'], 'requireState' => false, 'addressType' => 1, 'siteNomen' => 1]]]),
                '/v1/location/state' => self::jsonResponse(['states' => [['id' => 'IF', 'name' => 'Ilfov', 'nameEn' => 'Ilfov', 'countryId' => 642]]]),
                '/v1/location/site' => self::jsonResponse(['sites' => [['id' => 1, 'countryId' => 642, 'type' => 'Oras', 'typeEn' => 'Town', 'name' => 'Otopeni', 'nameEn' => 'Otopeni', 'municipality' => 'Otopeni', 'municipalityEn' => 'Otopeni', 'region' => 'Ilfov', 'regionEn' => 'Ilfov', 'addressNomenclature' => 1]]]),
                '/v1/location/street' => self::jsonResponse(['streets' => [['id' => 11, 'siteId' => 1, 'type' => 'Str.', 'typeEn' => 'Street', 'name' => 'Aeroportului', 'nameEn' => 'Airport']]]),
                '/v1/location/complex' => self::jsonResponse(['complexes' => [['id' => 9, 'siteId' => 1, 'type' => 'Bl.', 'typeEn' => 'Block', 'name' => 'A1', 'nameEn' => 'A1']]]),
                '/v1/location/block' => self::jsonResponse(['blocks' => [['siteId' => 1, 'name' => 'B2', 'nameEn' => 'B2']]]),
                '/v1/location/poi' => self::jsonResponse(['pois' => [['id' => 2, 'siteId' => 1, 'name' => 'Terminal', 'nameEn' => 'Terminal', 'type' => 'Aeroport', 'typeEn' => 'Airport', 'address' => 'Otopeni', 'addressEn' => 'Otopeni']]]),
                '/v1/location/office' => self::jsonResponse(['offices' => [['id' => 44, 'name' => 'Locker', 'nameEn' => 'Locker', 'siteId' => 1, 'type' => 'APT', 'cargoTypesAllowed' => ['PARCEL']]]]),
                default => new Response(404, ['Content-Type' => 'application/json'], '{}'),
            };
        });
        $client = TestClientFactory::create($httpClient);

        $countries = $client->locations()->findCountries(new CountrySearchRequest(name: 'Rom'));
        $countryPayload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());
        $states = $client->locations()->findStates(new StateSearchRequest(countryId: 642, name: 'Ilf'));
        $statePayload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());
        $sites = $client->locations()->findSites(new SiteSearchRequest(countryId: 642, municipality: 'Otopeni'));
        $streets = $client->locations()->findStreets(new StreetSearchRequest(siteId: 1, name: 'Aer'));
        $complexes = $client->locations()->findComplexes(new ComplexSearchRequest(siteId: 1, type: 'Bl.'));
        $blocks = $client->locations()->findBlocks(new BlockSearchRequest(siteId: 1, name: 'B'));
        $pois = $client->locations()->findPointsOfInterest(new PointOfInterestSearchRequest(siteId: 1, name: 'Term'));
        $offices = $client->locations()->findOffices(new OfficeSearchRequest(countryId: -1, name: 'Locker', limit: 1));
        $officePayload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());

        $this->assertSame('Rom', $countryPayload['name']);
        $this->assertSame(642, $states->first()?->countryId);
        $this->assertSame('Ilf', $statePayload['name']);
        $this->assertSame('Otopeni', $sites->first()?->municipality->local);
        $this->assertSame('Airport', $streets->first()?->name->english);
        $this->assertSame('Block', $complexes->first()?->type->english);
        $this->assertSame('B2', $blocks->first()?->name->local);
        $this->assertSame('Airport', $pois->first()?->type->english);
        $this->assertSame(-1, $officePayload['countryId']);
        $this->assertSame(1, $officePayload['limit']);
        $this->assertSame(OfficeType::Apt, $offices->first()?->type);
    }

    public function testBulkCsvEndpointsHydrateAndCacheReferenceData(): void
    {
        $requests = [];
        $client = $this->makeClient(
            static function (RequestInterface $request) use (&$requests): Response {
                $requests[] = $request->getUri()->getPath();

                return match ($request->getUri()->getPath()) {
                    '/v1/location/country/csv' => self::csvResponse(implode("\n", [
                        'id,name,nameEn,isoAlpha2,isoAlpha3,postCodeFormats,requireState,addressType,currencyCode,defaultOfficeId,streetTypes,streetTypesEn,complexTypes,complexTypesEn,siteNomen',
                        '642,Romania,Romania,RO,ROU,"NNNNNN,",0,1,RON,100,"Str.","Street","Bl.","Block",1',
                    ])),
                    '/v1/location/state/csv/642' => self::csvResponse(implode("\n", [
                        'id,name,nameEn,isoAlpha,countryId',
                        'B,Bucuresti,Bucharest,B,642',
                    ])),
                    '/v1/location/site/csv/642' => self::csvResponse(implode("\n", [
                        'id,countryId,mainSiteId,type,typeEn,name,nameEn,municipality,municipalityEn,region,regionEn,postCode,addressNomenclature,x,y,servingDays,servingOfficeId,servingHubOfficeId',
                        '68134,642,,Municipiu,Municipality,Bucuresti,Bucharest,Bucuresti,Bucharest,Bucuresti,Bucharest,010101,2,26.1,44.4,1111100,200,300',
                    ])),
                    '/v1/location/street/csv/642' => self::csvResponse(implode("\n", [
                        'id,siteId,type,typeEn,name,nameEn,actualId,actualType,actualTypeEn,actualName,actualNameEn',
                        '11,68134,Str.,Street,Victoriei,Victory,12,Bd.,Boulevard,Unirii,Union',
                    ])),
                    '/v1/location/complex/csv/642' => self::csvResponse(implode("\n", [
                        'id,siteId,type,typeEn,name,nameEn,actualId,actualType,actualTypeEn,actualName,actualNameEn',
                        '21,68134,Bl.,Block,Central,Central,,,,,',
                    ])),
                    '/v1/location/block/csv/642' => self::csvResponse(implode("\n", [
                        'siteId,name,nameEn',
                        '68134,B2,B2',
                    ])),
                    '/v1/location/poi/csv/642' => self::csvResponse(implode("\n", [
                        'id,siteId,name,nameEn,address,addressEn,x,y,type,typeEn',
                        '990,68134,Piata Romana,Roman Square,Sector 1,District 1,26.09,44.44,Piata,Square',
                    ])),
                    '/v1/location/postcode/csv/642' => self::csvResponse(implode("\n", [
                        'postcode,siteId',
                        '010101,68134',
                    ])),
                    default => new Response(404, ['Content-Type' => 'application/json'], '{}'),
                };
            },
            new ArrayReferenceDataCache(),
        );

        $countriesFirst = $client->locations()->allCountries(600);
        $countriesSecond = $client->locations()->allCountries(600);
        $states = $client->locations()->allStates(642, 600);
        $sites = $client->locations()->allSites(642, 600);
        $streets = $client->locations()->allStreets(642, 600);
        $complexes = $client->locations()->allComplexes(642, 600);
        $blocks = $client->locations()->allBlocks(642, 600);
        $pois = $client->locations()->allPointsOfInterest(642, 600);
        $postcodes = $client->locations()->allPostcodes(642, 600);

        $this->assertInstanceOf(Collection::class, $countriesFirst);
        $this->assertSame(8, count($requests));
        $this->assertSame('Block', $countriesFirst->first()?->complexTypes[0]->name->english);
        $this->assertSame($countriesFirst->first()?->id, $countriesSecond->first()?->id);
        $this->assertSame('Bucharest', $states->first()?->name->english);
        $this->assertSame('1111100', $sites->first()?->servingDays);
        $this->assertSame('Union', $streets->first()?->actualName?->english);
        $this->assertSame('Central', $complexes->first()?->name->local);
        $this->assertSame('B2', $blocks->first()?->name->english);
        $this->assertSame('Square', $pois->first()?->type->english);
        $this->assertSame('010101', $postcodes->first()?->postCode);
    }

    public function testNearestOfficesHydratesDistanceCoordinatesAndTypedFilters(): void
    {
        $httpClient = new FakeHttpClient(static function (RequestInterface $request): Response {
            return self::jsonResponse([
                'x' => 26.1025,
                'y' => 44.4268,
                'offices' => [[
                    'distance' => 240,
                    'id' => 100,
                    'name' => 'Locker Piata',
                    'nameEn' => 'Square Locker',
                    'siteId' => 68134,
                    'type' => 'APT',
                    'cardPaymentAllowed' => true,
                    'cashPaymentAllowed' => false,
                    'pickUpAllowed' => true,
                    'dropOffAllowed' => true,
                    'cargoTypesAllowed' => ['PARCEL'],
                ]],
            ]);
        });
        $client = TestClientFactory::create($httpClient);

        $response = $client->locations()->findNearestOffices(new NearestOfficesRequest(
            address: new LocalShipmentAddress(
                location: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti']),
                streetType: 'Str.',
                streetName: 'Victoriei',
                streetNo: '10',
            ),
            distance: 500,
            limit: 3,
            officeType: OfficeType::Apt,
            officeFeatures: [OfficeFeature::CardPayment],
        ));
        $payload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());
        $office = $response->offices->first();

        $this->assertSame('/v1/location/office/nearest-offices', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame(500, $payload['distance']);
        $this->assertSame('APT', $payload['officeType']);
        $this->assertSame(['CARD_PAYMENT'], $payload['officeFeatures']);
        $this->assertSame(26.1025, $response->x);
        $this->assertNotNull($office);
        $this->assertSame(240, $office->distance);
        $this->assertSame(OfficeType::Apt, $office->office->type);
    }

    public function testEmptyCsvResponseThrowsAndIsNotCached(): void
    {
        $calls = 0;
        $client = $this->makeClient(static function () use (&$calls): Response {
            $calls++;

            return self::csvResponse('');
        }, new ArrayReferenceDataCache());

        foreach ([1, 2] as $attempt) {
            try {
                $client->locations()->allCountries();
                $this->fail('Expected UnsupportedResponseException on attempt ' . $attempt);
            } catch (UnsupportedResponseException) {
            }
        }

        $this->assertSame(2, $calls);
    }

    public function testHtmlBodyWithoutCsvColumnsThrowsAndIsNotCached(): void
    {
        $calls = 0;
        $client = $this->makeClient(static function () use (&$calls): Response {
            $calls++;

            return self::csvResponse("<html>\n<body>error</body>\n</html>");
        }, new ArrayReferenceDataCache());

        foreach ([1, 2] as $attempt) {
            try {
                $client->locations()->allCountries();
                $this->fail('Expected UnsupportedResponseException on attempt ' . $attempt);
            } catch (UnsupportedResponseException) {
            }
        }

        $this->assertSame(2, $calls);
    }

    public function testUnknownAddressTypeDefaultsToLocal(): void
    {
        $country = \Daika7ana\Dpd\DTOs\Locations\Geography\Country::fromArray(['id' => 1, 'addressType' => 99]);

        $this->assertSame(AddressType::Local, $country->addressType);
    }

    public function testCachedReferenceDataReturnsIndependentCollections(): void
    {
        $client = $this->makeClient(
            static fn(): Response => self::csvResponse(implode("\n", [
                'id,name,nameEn,isoAlpha2,isoAlpha3,postCodeFormats,requireState,addressType,currencyCode,defaultOfficeId,streetTypes,streetTypesEn,complexTypes,complexTypesEn,siteNomen',
                '642,Romania,Romania,RO,ROU,"NNNNNN,",0,1,RON,100,"Str.","Street","Bl.","Block",1',
            ])),
            new ArrayReferenceDataCache(),
        );

        $first = $client->locations()->allCountries();
        $first[] = 'junk';
        $second = $client->locations()->allCountries();

        $this->assertCount(1, $second);
    }

    private function makeClient(callable $handler, ?ArrayReferenceDataCache $cache = null): Client
    {
        return TestClientFactory::create(new FakeHttpClient($handler), $cache);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function jsonResponse(array $payload): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($payload, JSON_THROW_ON_ERROR));
    }

    private static function csvResponse(string $payload): Response
    {
        return new Response(200, ['Content-Type' => 'text/csv'], $payload);
    }
}
