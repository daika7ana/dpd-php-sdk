<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelSize;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\MidwayCarrierParcelHandover;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ParcelHandover;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentAdditionalServices;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentCodAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentParcel;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentRecipient;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentSender;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\DTOs\Shipments\Requests\AddParcelRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CancelShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\FindParcelsByReferenceRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\FinalizePendingShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\HandoverToCourierRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\HandoverToMidwayCarrierRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\SecondaryShipmentsRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\ShipmentInformationRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\UpdateShipmentPropertiesRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\UpdateShipmentRequest;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Shipments\CodProcessingType;
use Daika7ana\Dpd\Enums\Shipments\SearchInReference;
use Daika7ana\Dpd\Enums\Shipments\SecondaryShipmentType;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use DateTimeImmutable;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ShipmentsResourceTest extends TestCase
{
    public function testCreateUsesTypedPayloadAndMapsCreateResponse(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id' => 'SHIP123',
            'pickupDate' => '2026-04-27',
            'deliveryDeadline' => '2026-04-28T12:00:00+03:00',
            'parcels' => [
                ['seqNo' => 1, 'id' => 'PARCEL123'],
            ],
            'price' => [
                'amount' => 10.0,
                'vat' => 1.9,
                'total' => 11.9,
                'currency' => 'RON',
                'details' => ['NET' => ['amount' => 10.0, 'vatPercent' => 0.19]],
                'amountLocal' => 10.0,
                'vatLocal' => 1.9,
                'totalLocal' => 11.9,
                'currencyLocal' => 'RON',
                'detailsLocal' => ['NET' => ['amount' => 10.0, 'vatPercent' => 0.19]],
                'currencyExchangeRateUnit' => 1,
                'currencyExchangeRate' => 1.0,
            ],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $response = $client->shipments()->create($this->makeCreateRequest());
        $payload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());

        $this->assertSame('/v1/shipment', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('demo-user', $payload['userName']);
        $this->assertSame('demo-password', $payload['password']);
        $this->assertSame('Str.', $payload['sender']['address']['streetType']);
        $this->assertSame('CASH', $payload['service']['additionalServices']['cod']['processingType']);
        $this->assertSame('SHIP123', $response->id);
        $this->assertSame('PARCEL123', $response->parcels->first()?->id);
        $this->assertSame('RON', $response->price?->currency);
    }

    public function testCoreLifecycleEndpointsUseExpectedPaths(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id' => 'SHIP123',
            'parcels' => [['seqNo' => 1, 'id' => 'PARCEL123']],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $client->shipments()->finalizePendingShipment(new FinalizePendingShipmentRequest('SHIP123'));
        $this->assertSame('/v1/shipment/finalize', $httpClient->lastRequest?->getUri()->getPath());

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);
        $client->shipments()->cancel(new CancelShipmentRequest('SHIP123', 'Cancelled by test'));
        $this->assertSame('POST', $httpClient->lastRequest?->getMethod());
        $this->assertSame('/v1/shipment/cancel', $httpClient->lastRequest?->getUri()->getPath());

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);
        $client->shipments()->cancelWithDelete(new CancelShipmentRequest('SHIP123', 'Cancelled by test'));
        $this->assertSame('DELETE', $httpClient->lastRequest?->getMethod());
        $this->assertSame('/v1/shipment', $httpClient->lastRequest?->getUri()->getPath());

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'parcel' => ['seqNo' => 2, 'id' => 'PARCEL124'],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);
        $client->shipments()->addParcel(new AddParcelRequest('SHIP123', new ShipmentParcel(seqNo: 2, weight: 1.2)));
        $this->assertSame('/v1/shipment/add_parcel', $httpClient->lastRequest?->getUri()->getPath());

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id' => 'SHIP123',
            'parcels' => [['seqNo' => 1, 'id' => 'PARCEL123']],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);
        $client->shipments()->update(new UpdateShipmentRequest('SHIP123', $this->makeCreateRequest()));
        $this->assertSame('/v1/shipment/update', $httpClient->lastRequest?->getUri()->getPath());

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id' => 'SHIP123',
            'parcels' => [['seqNo' => 1, 'id' => 'PARCEL123']],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);
        $client->shipments()->updateProperties(new UpdateShipmentPropertiesRequest('SHIP123', ['ref1' => 'UPDATED-REF']));
        $this->assertSame('/v1/shipment/update/properties', $httpClient->lastRequest?->getUri()->getPath());
    }

    public function testUpdateShipmentPropertiesNormalizesSupportedNestedPaths(): void
    {
        $request = new UpdateShipmentPropertiesRequest('SHIP123', [
            'content.parcels[0].weight' => '2.40',
            'recipient.phone1.number' => '+40799111111',
        ]);

        $this->assertSame([
            'content.parcels.0.weight' => '2.40',
            'recipient.phone1.number' => '+40799111111',
        ], $request->properties);
    }

    public function testUpdateShipmentPropertiesRejectsUnsupportedPaths(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported shipment property path "recipient.phone4.number".');

        new UpdateShipmentPropertiesRequest('SHIP123', ['recipient.phone4.number' => '+40799111111']);
    }

    public function testShipmentInfoMapsTypedShipmentRecords(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'shipments' => [
                [
                    'id' => 'SHIP123',
                    'sender' => [
                        'clientId' => 10,
                        'clientName' => 'Sender SRL',
                        'address' => ['countryId' => 642, 'siteId' => 12345, 'streetName' => 'Libertatii', 'streetNo' => '10'],
                    ],
                    'recipient' => [
                        'clientName' => 'Receiver SRL',
                        'pickupOfficeId' => 44,
                    ],
                    'service' => [
                        'serviceId' => 2505,
                        'additionalServices' => ['cod' => ['amount' => 99.9, 'processingType' => 'CASH']],
                    ],
                    'content' => [
                        'parcelsCount' => 1,
                        'declaredWeight' => 2.5,
                        'parcels' => [
                            ['id' => 'PARCEL123', 'seqNo' => 1, 'declaredWeight' => 2.5],
                        ],
                    ],
                    'payment' => [
                        'courierServicePayer' => 'SENDER',
                        'codPayment' => ['date' => '2026-04-29T10:00:00+03:00', 'totalPayedOutAmount' => 99.9],
                    ],
                    'delivery' => ['deadline' => '2026-04-28T18:00:00+03:00'],
                    'primaryShipment' => ['id' => 'PRIMARY1', 'type' => 'RETURN_SHIPMENT'],
                    'pendingShipment' => false,
                ],
            ],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $response = $client->shipments()->info(new ShipmentInformationRequest(['SHIP123']));
        $shipment = $response->shipments->first();

        $this->assertNotNull($shipment);
        $this->assertSame('/v1/shipment/info', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('Sender SRL', $shipment->sender?->clientName);
        $this->assertSame(2505, $shipment->service?->serviceId);
        $this->assertSame(2.5, $shipment->content?->declaredWeight);
        $this->assertSame(99.9, $shipment->payment?->codPayment?->totalPayedOutAmount);
        $this->assertSame(SecondaryShipmentType::ReturnShipment, $shipment->primaryShipment?->type);
    }

    public function testSearchAndBarcodeInformationMapTypedResponses(): void
    {
        $searchHttpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'barcodes' => ['PARCEL123', 'PARCEL124'],
        ], JSON_THROW_ON_ERROR)));
        $searchClient = $this->makeClient($searchHttpClient);

        $searchResponse = $searchClient->shipments()->findParcelsByReference(new FindParcelsByReferenceRequest('ORDER-100', SearchInReference::Ref1OrRef2));
        $this->assertSame('/v1/shipment/search', $searchHttpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('PARCEL123', $searchResponse->barcodes->first());

        $barcodeHttpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'labelInfo' => ['parcelId' => 'PARCEL123', 'fullBarcode' => '123456789012345678', 'exportPriority' => 1],
            'primaryShipment' => ['id' => 'SHIP123', 'type' => 'RETURN_SHIPMENT'],
            'primaryParcelId' => 'PARCEL123',
        ], JSON_THROW_ON_ERROR)));
        $barcodeClient = $this->makeClient($barcodeHttpClient);

        $barcodeResponse = $barcodeClient->shipments()->barcodeInformation(ShipmentParcelRef::fromArray(['id' => 'PARCEL123']));
        $this->assertSame('/v1/shipment/barcode-information', $barcodeHttpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('PARCEL123', $barcodeResponse->labelInfo?->parcelId);
        $this->assertSame(SecondaryShipmentType::ReturnShipment, $barcodeResponse->primaryShipment?->type);
    }

    public function testSecondaryAndHandoverEndpointsUseTypedPayloads(): void
    {
        $secondaryHttpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'shipments' => [
                [
                    'id' => 'SEC123',
                    'type' => 'RETURN_SHIPMENT',
                    'parcels' => [['id' => 'PARCEL123', 'seqNo' => 1]],
                    'serviceId' => 2505,
                    'hasScans' => true,
                ],
            ],
        ], JSON_THROW_ON_ERROR)));
        $secondaryClient = $this->makeClient($secondaryHttpClient);

        $secondaryResponse = $secondaryClient->shipments()->secondaryShipments(new SecondaryShipmentsRequest('SHIP123', [SecondaryShipmentType::ReturnShipment]));
        $secondaryPayload = JsonHelper::decode((string) $secondaryHttpClient->lastRequest?->getBody());
        $this->assertSame('/v1/shipment/SHIP123/secondary', $secondaryHttpClient->lastRequest?->getUri()->getPath());
        $this->assertSame(['RETURN_SHIPMENT'], $secondaryPayload['types']);
        $this->assertSame('SEC123', $secondaryResponse->shipments->first()?->id);

        $handoverHttpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([], JSON_THROW_ON_ERROR)));
        $handoverClient = $this->makeClient($handoverHttpClient);
        $handoverClient->shipments()->handoverToCourier(new HandoverToCourierRequest([
            new ParcelHandover(ShipmentParcelRef::fromArray(['id' => 'PARCEL123']), new DateTimeImmutable('2026-04-27T09:00:00+03:00')),
        ]));
        $handoverPayload = JsonHelper::decode((string) $handoverHttpClient->lastRequest?->getBody());
        $this->assertSame('/v1/shipment/handover', $handoverHttpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('PARCEL123', $handoverPayload['parcels'][0]['id']);

        $midwayHttpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([], JSON_THROW_ON_ERROR)));
        $midwayClient = $this->makeClient($midwayHttpClient);
        $midwayClient->shipments()->handoverToMidwayCarrier(new HandoverToMidwayCarrierRequest([
            new MidwayCarrierParcelHandover('PARCEL123', 642, new DateTimeImmutable('2026-04-27T10:00:00+03:00'), 'Bucuresti'),
        ]));
        $midwayPayload = JsonHelper::decode((string) $midwayHttpClient->lastRequest?->getBody());
        $this->assertSame('/v1/shipment/handover-to-midway-carrier', $midwayHttpClient->lastRequest?->getUri()->getPath());
        $this->assertSame(642, $midwayPayload['parcels'][0]['countryId']);
    }

    private function makeClient(FakeHttpClient $httpClient): Client
    {
        $factory = new HttpFactory();

        return Client::create(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(
                username: 'demo-user',
                password: 'demo-password',
                language: 'EN',
                clientSystemId: 55,
            ),
        );
    }

    private function makeCreateRequest(): CreateShipmentRequest
    {
        return new CreateShipmentRequest(
            sender: new ShipmentSender(
                phone1: new ShipmentPhoneNumber('+40799111111'),
                clientName: 'Sender SRL',
                privatePerson: false,
                address: new LocalShipmentAddress(
                    location: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 12345, 'siteName' => 'Bucuresti', 'postCode' => '010101']),
                    streetType: 'Str.',
                    streetName: 'Libertatii',
                    streetNo: '10',
                ),
            ),
            recipient: new ShipmentRecipient(
                phone1: new ShipmentPhoneNumber('+40799222222'),
                clientName: 'Receiver SRL',
                privatePerson: false,
                address: new LocalShipmentAddress(
                    location: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 12345, 'siteName' => 'Bucuresti', 'postCode' => '010101']),
                    streetType: 'Str.',
                    streetName: 'Victoriei',
                    streetNo: '12',
                ),
            ),
            service: new ShipmentService(
                serviceId: 2505,
                pickupDate: new DateTimeImmutable('2026-04-27'),
                additionalServices: new ShipmentAdditionalServices(
                    cod: new ShipmentCodAdditionalService(amount: 99.9, processingType: CodProcessingType::Cash),
                ),
            ),
            content: new ShipmentContent(
                contents: 'Electronics',
                package: 'Box',
                parcels: [
                    new ShipmentParcel(seqNo: 1, weight: 2.5, size: new ShipmentParcelSize(width: 10, depth: 20, height: 30)),
                ],
            ),
            payment: new ShipmentPayment(Payer::Sender),
            ref1: 'ORDER-100',
        );
    }
}
