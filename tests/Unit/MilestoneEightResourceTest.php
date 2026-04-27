<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationContent;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationRecipient;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationSender;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationService;
use Daika7ana\Dpd\DTOs\Calculations\Requests\CalculationRequest;
use Daika7ana\Dpd\DTOs\Calculations\Requests\DestinationServicesRequest;
use Daika7ana\Dpd\DTOs\Clients\Requests\CreateContactRequest;
use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Payments\PayoutDocumentType;
use Daika7ana\Dpd\Enums\Payments\PayoutPaymentType;
use Daika7ana\Dpd\Enums\Services\AdditionalServiceAllowance;
use Daika7ana\Dpd\Enums\Services\CargoType;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use DateTimeImmutable;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class MilestoneEightResourceTest extends TestCase
{
    public function testDestinationServicesAndCalculationsMapTypedDtos(): void
    {
        $payloads = [];
        $httpClient = new FakeHttpClient(static function (RequestInterface $request) use (&$payloads): Response {
            $payloads[$request->getUri()->getPath()] = JsonHelper::decode((string) $request->getBody());

            return match ($request->getUri()->getPath()) {
                '/v1/services/destination' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'services' => [
                        [
                            'id' => 2505,
                            'name' => 'Courier',
                            'nameEn' => 'Courier',
                            'cargoType' => 'PARCEL',
                            'requireParcelWeight' => true,
                            'requireParcelSize' => false,
                            'deadline' => '2026-04-29T18:00:00+03:00',
                            'additionalServices' => ['cod' => ['allowance' => 'ALLOWED']],
                        ],
                    ],
                ], JSON_THROW_ON_ERROR)),
                '/v1/calculate' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'calculations' => [
                        [
                            [
                                'serviceId' => 2505,
                                'pickupDate' => '2026-04-28',
                                'deliveryDeadline' => '2026-04-29T18:00:00+03:00',
                                'additionalServices' => ['cod' => ['amount' => 50.0]],
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
                            ],
                        ],
                    ],
                ], JSON_THROW_ON_ERROR)),
                default => new Response(404, ['Content-Type' => 'application/json'], '{}'),
            };
        });
        $client = $this->makeClient($httpClient);
        $recipient = new CalculationRecipient(addressLocation: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti', 'postCode' => '010101']));

        $services = $client->services()->destination(new DestinationServicesRequest(
            recipient: $recipient,
            date: new DateTimeImmutable('2026-04-28'),
            sender: new CalculationSender(addressLocation: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti'])),
        ));
        $calculations = $client->calculations()->calculate(new CalculationRequest(
            recipient: $recipient,
            service: new CalculationService(serviceIds: [2505], pickupDate: new DateTimeImmutable('2026-04-28')),
            content: new CalculationContent(parcelsCount: 1, totalWeight: 2.5),
            payment: new ShipmentPayment(Payer::Sender),
        ));

        $this->assertSame('2026-04-28', $payloads['/v1/services/destination']['date']);
        $this->assertSame(CargoType::Parcel, $services->first()?->cargoType);
        $this->assertSame(AdditionalServiceAllowance::Allowed, $services->first()?->additionalServices->cod?->allowance);
        $this->assertSame([2505], $payloads['/v1/calculate']['service']['serviceIds']);
        $this->assertSame(2505, $calculations->first()?->first()?->serviceId);
        $this->assertSame('RON', $calculations->first()?->first()?->price?->currency);
    }

    public function testClientsAndPaymentsMapTypedResponses(): void
    {
        $payloads = [];
        $httpClient = new FakeHttpClient(static function (RequestInterface $request) use (&$payloads): Response {
            $payloads[$request->getUri()->getPath()] = JsonHelper::decode((string) $request->getBody());

            return match ($request->getUri()->getPath()) {
                '/v1/client/15' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'client' => [
                        'clientId' => 15,
                        'clientName' => 'Client SRL',
                        'address' => ['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti', 'streetName' => 'Victoriei', 'streetNo' => '10'],
                        'privatePerson' => false,
                    ],
                ], JSON_THROW_ON_ERROR)),
                '/v1/client/contract' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'clients' => [
                        ['clientId' => 15, 'clientName' => 'Client SRL'],
                        ['clientId' => 16, 'clientName' => 'Branch SRL'],
                    ],
                ], JSON_THROW_ON_ERROR)),
                '/v1/client/contact' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'clientId' => 99,
                ], JSON_THROW_ON_ERROR)),
                '/v1/client/contact/external/external-1' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'client' => ['clientId' => 99, 'clientName' => 'External Contact'],
                ], JSON_THROW_ON_ERROR)),
                '/v1/client' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'clientId' => 15,
                ], JSON_THROW_ON_ERROR)),
                '/v1/client/contract/info' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'id' => 700,
                    'administrativeFeeAllowed' => true,
                    'specialDeliveryRequirements' => [
                        'requiredForAllShipments' => true,
                        'requirements' => [['id' => 1, 'text' => 'Call before delivery']],
                    ],
                    'cod' => [
                        'moneyTransferAllowed' => true,
                        'codFiscalReceiptAllowed' => false,
                        'hasCODAnnex' => true,
                        'internationalCODAnnexes' => [['countryId' => 642, 'countryName' => 'Romania']],
                    ],
                ], JSON_THROW_ON_ERROR)),
                '/v1/payments' => new Response(200, ['Content-Type' => 'application/json'], json_encode([
                    'payouts' => [
                        [
                            'date' => '2026-04-27',
                            'docId' => 321,
                            'docType' => 'CASH',
                            'paymentType' => 'BANK',
                            'payee' => 'Client SRL',
                            'currency' => 'RON',
                            'amount' => 99.5,
                            'details' => [
                                [
                                    'lineNo' => 1,
                                    'shipmentId' => 'SHIP123',
                                    'pickupDate' => '2026-04-26',
                                    'amount' => 99.5,
                                ],
                            ],
                        ],
                    ],
                ], JSON_THROW_ON_ERROR)),
                default => new Response(404, ['Content-Type' => 'application/json'], '{}'),
            };
        });
        $client = $this->makeClient($httpClient);

        $contactId = $client->clients()->createContact(new CreateContactRequest(
            externalContactId: 'external-1',
            phone1: new ShipmentPhoneNumber('+40799111111'),
            clientName: 'External Contact',
            privatePerson: false,
            address: new LocalShipmentAddress(
                location: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 68134, 'siteName' => 'Bucuresti', 'postCode' => '010101']),
                streetType: 'Str.',
                streetName: 'Victoriei',
                streetNo: '10',
            ),
            secretKey: 'top-secret',
        ));

        $clientRecord = $client->clients()->get(15);
        $contractClients = $client->clients()->contractClients();
        $externalClient = $client->clients()->contactByExternalId('external-1', 'top-secret');
        $ownClientId = $client->clients()->ownClientId();
        $contractInfo = $client->clients()->contractInfo();
        $payouts = $client->payments()->payouts(new \Daika7ana\Dpd\DTOs\Payments\Requests\PayoutRequest(
            fromDate: new DateTimeImmutable('2026-04-01T00:00:00+03:00'),
            toDate: new DateTimeImmutable('2026-04-30T23:59:59+03:00'),
            includeDetails: true,
        ));

        $this->assertSame('external-1', $payloads['/v1/client/contact']['externalContactId']);
        $this->assertSame('Victoriei', $payloads['/v1/client/contact']['address']['streetName']);
        $this->assertSame('top-secret', $payloads['/v1/client/contact/external/external-1']['secretKey']);
        $this->assertSame(99, $contactId);
        $this->assertSame('Client SRL', $clientRecord?->clientName);
        $this->assertCount(2, $contractClients);
        $this->assertSame(15, $ownClientId);
        $this->assertSame(700, $contractInfo->id);
        $this->assertTrue($contractInfo->administrativeFeeAllowed);
        $this->assertTrue($contractInfo->cod?->moneyTransferAllowed ?? false);
        $this->assertSame('External Contact', $externalClient?->clientName);
        $this->assertSame(PayoutDocumentType::Cash, $payouts->first()?->docType);
        $this->assertSame(PayoutPaymentType::Bank, $payouts->first()?->paymentType);
        $this->assertSame('SHIP123', $payouts->first()?->details->first()?->shipmentId);
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
