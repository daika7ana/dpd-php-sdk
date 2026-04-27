<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationSender;
use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelSize;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupRequest;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupTermsRequest;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentParcel;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentRecipient;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\DTOs\Validation\Requests\ValidatePostCodeRequest;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Pickup\PickupScope;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use DateTimeImmutable;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class PickupAndValidationResourceTest extends TestCase
{
    public function testPickupEndpointsMapOrdersAndCutoffs(): void
    {
        $payloads = [];
        $httpClient = new FakeHttpClient(static function (RequestInterface $request) use (&$payloads): Response {
            $payloads[$request->getUri()->getPath()] = JsonHelper::decode((string) $request->getBody());

            return match ($request->getUri()->getPath()) {
                '/v1/pickup' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'orders' => [
                        [
                            'id' => 91,
                            'shipmentIds' => ['SHIP1', 'SHIP2'],
                            'pickupPeriodFrom' => '2026-04-28T09:00:00+03:00',
                            'pickupPeriodTo' => '2026-04-28T11:00:00+03:00',
                        ],
                    ],
                ], JSON_THROW_ON_ERROR)),
                '/v1/pickup/terms' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'cutoffs' => ['2026-04-28T15:00:00+03:00', '2026-04-29T15:00:00+03:00'],
                ], JSON_THROW_ON_ERROR)),
                default => new Response(404, ['Content-Type' => 'application/json'], '{}'),
            };
        });
        $client = $this->makeClient($httpClient);

        $orders = $client->pickup()->order(new PickupRequest(
            visitEndTime: '11:30',
            pickupDateTime: new DateTimeImmutable('2026-04-28T09:15:00+03:00'),
            pickupScope: PickupScope::ExplicitShipmentIdList,
            explicitShipmentIdList: ['SHIP1', 'SHIP2'],
            contactName: 'Warehouse',
            phoneNumber: new ShipmentPhoneNumber('+40799111111'),
        ));
        $cutoffs = $client->pickup()->terms(new PickupTermsRequest(
            serviceId: 2505,
            startingDate: new DateTimeImmutable('2026-04-28'),
            sender: new CalculationSender(addressLocation: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti'])),
            senderHasPayment: true,
        ));

        $this->assertSame('EXPLICIT_SHIPMENT_ID_LIST', $payloads['/v1/pickup']['pickupScope']);
        $this->assertSame('SHIP1', $payloads['/v1/pickup']['explicitShipmentIdList'][0]);
        $this->assertSame('Warehouse', $payloads['/v1/pickup']['contactName']);
        $this->assertSame(91, $orders->first()?->id);
        $this->assertSame('SHIP2', $orders->first()?->shipmentIds->last());
        $this->assertSame('2026-04-28T15:00:00+03:00', $cutoffs->first());
        $this->assertSame(2505, $payloads['/v1/pickup/terms']['serviceId']);
    }

    public function testValidationEndpointsUseTypedPayloads(): void
    {
        $paths = [];
        $payloads = [];
        $httpClient = new FakeHttpClient(static function (RequestInterface $request) use (&$paths, &$payloads): Response {
            $paths[] = $request->getUri()->getPath();
            $payloads[$request->getUri()->getPath()] = JsonHelper::decode((string) $request->getBody());

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'valid' => true,
            ], JSON_THROW_ON_ERROR));
        });
        $client = $this->makeClient($httpClient);
        $address = new LocalShipmentAddress(
            location: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti', 'postCode' => '010101']),
            streetType: 'Str.',
            streetName: 'Victoriei',
            streetNo: '10',
        );

        $this->assertTrue($client->validation()->address($address)->valid);
        $this->assertTrue($client->validation()->postCode(new ValidatePostCodeRequest('010101', 642))->valid);
        $this->assertTrue($client->validation()->phone('+40799111111', '12')->valid);
        $this->assertTrue($client->validation()->shipment($this->makeCreateShipmentRequest($address))->valid);

        $this->assertSame('/v1/validation/address', $paths[0]);
        $this->assertSame('Victoriei', $payloads['/v1/validation/address']['address']['streetName']);
        $this->assertSame('010101', $payloads['/v1/validation/postcode']['postCode']);
        $this->assertSame('12', $payloads['/v1/validation/phone']['ext']);
        $this->assertSame('Receiver SRL', $payloads['/v1/validation/shipment']['recipient']['clientName']);
    }

    private function makeCreateShipmentRequest(LocalShipmentAddress $address): CreateShipmentRequest
    {
        return new CreateShipmentRequest(
            recipient: new ShipmentRecipient(
                phone1: new ShipmentPhoneNumber('+40799222222'),
                clientName: 'Receiver SRL',
                privatePerson: false,
                address: $address,
            ),
            service: new ShipmentService(serviceId: 2505, pickupDate: new DateTimeImmutable('2026-04-28')),
            content: new ShipmentContent(
                contents: 'Electronics',
                package: 'Box',
                parcels: [new ShipmentParcel(seqNo: 1, weight: 2.5, size: new ShipmentParcelSize(width: 10, depth: 20, height: 30))],
            ),
            payment: new ShipmentPayment(Payer::Sender),
        );
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
}
