<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Printing\Payloads\ParcelToPrint;
use Daika7ana\Dpd\DTOs\Printing\Requests\PrintRequest;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ParcelToPrintAdditionalBarcode;
use Daika7ana\Dpd\Enums\Printing\AdditionalBarcodeValueType;
use Daika7ana\Dpd\Enums\Printing\AdditionalWaybillSenderCopy;
use Daika7ana\Dpd\Enums\Printing\BarcodeFormat;
use Daika7ana\Dpd\Enums\Printing\Dpi;
use Daika7ana\Dpd\Enums\Printing\PaperSize;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use Daika7ana\Dpd\Exceptions\ValidationException;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class PrintingResourceTest extends TestCase
{
    public function testPrintingEndpointsHandleBinaryAndJsonPayloads(): void
    {
        $request = new PrintRequest(
            paperSize: PaperSize::A6,
            parcels: [
                new ParcelToPrint(
                    parcel: new ShipmentParcelRef('PARCEL123', null, null),
                    additionalBarcode: new ParcelToPrintAdditionalBarcode(
                        value: '998877',
                        label: '998877',
                        format: BarcodeFormat::Ean8,
                        valueType: AdditionalBarcodeValueType::Provided,
                    ),
                ),
            ],
            format: PrintFormat::Pdf,
            dpi: Dpi::Dpi300,
            additionalWaybillSenderCopy: AdditionalWaybillSenderCopy::OnSinglePage,
        );

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-test'));
        $client = $this->makeClient($httpClient);

        $document = $client->printing()->document($request);
        $payload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());

        $this->assertTrue($document->isPdf());
        $this->assertSame('%PDF-test', $document->contents);
        $this->assertSame('/v1/print', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('pdf', $payload['format']);
        $this->assertSame('A6', $payload['paperSize']);
        $this->assertSame('PARCEL123', $payload['parcels'][0]['parcel']['id']);
        $this->assertSame('ON_SINGLE_PAGE', $payload['additionalWaybillSenderCopy']);

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => base64_encode('%PDF-extended'),
            'printLabelsInfo' => [
                ['parcelId' => 'PARCEL123', 'fullBarcode' => '123456789012', 'exportPriority' => 1],
            ],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $extended = $client->printing()->extended($request);
        $this->assertSame('%PDF-extended', $extended->asDocument()->contents);
        $this->assertSame('PARCEL123', $extended->printLabelsInfo->first()?->parcelId);

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'printLabelsInfo' => [
                ['parcelId' => 'PARCEL123', 'fullBarcode' => '123456789012', 'exportPriority' => 2],
            ],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $labelInfo = $client->printing()->labelInfo([new ShipmentParcelRef('PARCEL123', null, null)]);
        $this->assertSame('/v1/print/labelInfo', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame(2, $labelInfo->printLabelsInfo->first()?->exportPriority);

        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'text/plain'], '^XA^FO50,50^FDvoucher^FS^XZ'));
        $client = $this->makeClient($httpClient);

        $voucher = $client->printing()->voucher(['SHIP123']);
        $this->assertTrue($voucher->isZpl());
        $this->assertSame('^XA^FO50,50^FDvoucher^FS^XZ', $voucher->contents);
        $this->assertSame('/v1/print/voucher', $httpClient->lastRequest?->getUri()->getPath());
    }

    public function testPrintingDocumentRaisesValidationExceptionForJsonErrors(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'error' => [
                'code' => 120,
                'message' => 'Invalid address.',
                'id' => 'err-120',
            ],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid address.');

        $client->printing()->document(new PrintRequest(
            paperSize: PaperSize::A4,
            parcels: [new ParcelToPrint(new ShipmentParcelRef('PARCEL123', null, null))],
        ));
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
