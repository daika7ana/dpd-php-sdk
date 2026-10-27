<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\ForeignShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentAdditionalServices;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentCodAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentCodFiscalReceiptItem;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentDeclaredValueAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentElectronicReturnReceiptAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentObpd;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentRecipient;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentReturnAdditionalServices;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentReturnVoucherAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentRodAdditionalService;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentSender;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentInformationResponse;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Shipments\CodProcessingType;
use Daika7ana\Dpd\Enums\Shipments\ObpdOption;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ShipmentRequestDtosTest extends TestCase
{
    public function testCreateShipmentRequestKeepsLocalAndForeignAddressVariantsExplicit(): void
    {
        $sender = new ShipmentSender(
            phone1: new ShipmentPhoneNumber('+40799111111'),
            clientName: 'Sender SRL',
            objectName: 'Warehouse',
            privatePerson: false,
            address: new LocalShipmentAddress(
                location: AddressLocation::fromArray(['countryId' => 642, 'siteId' => 12345, 'siteName' => 'Bucuresti', 'postCode' => '010101']),
                streetType: 'Str.',
                streetName: 'Libertatii',
                streetNo: '10',
                blockNo: 'A1',
            ),
        );
        $recipient = new ShipmentRecipient(
            phone1: new ShipmentPhoneNumber('+49301234567'),
            clientName: 'Receiver GmbH',
            privatePerson: false,
            address: new ForeignShipmentAddress(
                location: AddressLocation::fromArray(['countryId' => 276, 'stateId' => 'BE', 'siteName' => 'Berlin', 'postCode' => '10115']),
                addressLine1: 'Alexanderplatz 1',
                addressLine2: 'Floor 2',
            ),
            email: 'ops@example.test',
        );
        $service = new ShipmentService(
            serviceId: 2505,
            pickupDate: new DateTimeImmutable('2026-04-27'),
            additionalServices: new ShipmentAdditionalServices(
                cod: new ShipmentCodAdditionalService(
                    amount: 129.99,
                    currencyCode: 'RON',
                    processingType: CodProcessingType::Cash,
                    fiscalReceiptItems: [
                        new ShipmentCodFiscalReceiptItem('SKU-1', 'B', 109.24, 129.99),
                    ],
                ),
                obpd: new ShipmentObpd(ObpdOption::Test, 2505, Payer::Sender),
                declaredValue: new ShipmentDeclaredValueAdditionalService(150.0, fragile: true),
                returns: new ShipmentReturnAdditionalServices(
                    rod: new ShipmentRodAdditionalService(enabled: true, comment: 'Return signed docs'),
                    electronicReturnReceipt: new ShipmentElectronicReturnReceiptAdditionalService(['docs@example.test']),
                    returnVoucher: new ShipmentReturnVoucherAdditionalService(2505, Payer::Recipient, 30),
                ),
                specialDeliveryId: 7,
                deliveryToFloor: 4,
            ),
        );

        $payload = new CreateShipmentRequest(
            sender: $sender,
            recipient: $recipient,
            service: $service,
            content: new ShipmentContent(contents: 'Electronics', package: 'Box', parcelsCount: 1, totalWeight: 2.5),
            payment: new ShipmentPayment(Payer::Sender),
            ref1: 'ORDER-100',
        );

        $payloadArray = $payload->toArray();

        $this->assertArrayHasKey('streetName', $payloadArray['sender']['address']);
        $this->assertArrayNotHasKey('addressLine1', $payloadArray['sender']['address']);
        $this->assertSame('Libertatii', $payloadArray['sender']['address']['streetName']);
        $this->assertArrayHasKey('addressLine1', $payloadArray['recipient']['address']);
        $this->assertArrayNotHasKey('streetName', $payloadArray['recipient']['address']);
        $this->assertSame('Alexanderplatz 1', $payloadArray['recipient']['address']['addressLine1']);
        $this->assertSame('TEST', $payloadArray['service']['additionalServices']['obpd']['option']);
        $this->assertSame('RON', $payloadArray['service']['additionalServices']['cod']['currencyCode']);
        $this->assertSame('Warehouse', $payloadArray['sender']['objectName']);
        $this->assertSame('Return signed docs', $payloadArray['service']['additionalServices']['returns']['rod']['comment']);
        $this->assertSame(['docs@example.test'], $payloadArray['service']['additionalServices']['returns']['electronicReturnReceipt']['recipientEmails']);
    }

    public function testShipmentInformationResponseMapsOptionalNestedStructuresWithoutLeakingInvalidItems(): void
    {
        $response = ShipmentInformationResponse::fromArray([
            'shipments' => [
                [
                    'id' => 'SHIP-100',
                    'sender' => [
                        'clientName' => 'Sender SRL',
                        'address' => [
                            'countryId' => 642,
                            'siteId' => 12345,
                            'siteName' => 'Bucuresti',
                            'postCode' => '010101',
                            'streetType' => 'Str.',
                            'streetName' => 'Libertatii',
                            'streetNo' => '10',
                        ],
                    ],
                    'recipient' => [
                        'clientName' => 'Receiver GmbH',
                        'address' => [
                            'countryId' => 276,
                            'stateId' => 'BE',
                            'siteName' => 'Berlin',
                            'postCode' => '10115',
                            'addressLine1' => 'Alexanderplatz 1',
                        ],
                    ],
                    'content' => [
                        'parcelsCount' => 1,
                        'parcels' => [
                            ['id' => 'PARCEL-1', 'seqNo' => 1],
                            'invalid-parcel',
                        ],
                    ],
                    'payment' => [
                        'courierServicePayer' => 'SENDER',
                    ],
                ],
                'invalid-shipment',
            ],
        ]);

        $shipment = $response->shipments->first();

        $this->assertSame(1, $response->shipments->count());
        $this->assertNotNull($shipment);
        $this->assertTrue($shipment->sender?->address?->usesLocalAddressing());
        $this->assertTrue($shipment->recipient?->address?->usesForeignAddressing());
        $this->assertNull($shipment->price);
        $this->assertSame(1, $shipment->content?->parcels?->count());
        $this->assertSame('PARCEL-1', $shipment->content?->parcels?->first()?->id);
    }
}
