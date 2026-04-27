<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class TrackingResourceTest extends TestCase
{
    public function testTrackBatchedSplitsRequestsAndMergesTypedResults(): void
    {
        $payloadSizes = [];
        $httpClient = new FakeHttpClient(static function (RequestInterface $request) use (&$payloadSizes): Response {
            $payload = \Daika7ana\Dpd\Support\JsonHelper::decode((string) $request->getBody());
            $payloadSizes[] = count($payload['parcels'] ?? []);
            $responseParcels = [];

            foreach ($payload['parcels'] ?? [] as $parcel) {
                $parcelId = (string) ($parcel['id'] ?? $parcel['fullBarcode'] ?? $parcel['externalCarrierParcelNumber'] ?? $parcel['ref'] ?? '');
                $responseParcels[] = [
                    'parcelId' => $parcelId,
                    'externalCarrierParcelNumbers' => ['EXT-' . $parcelId],
                    'externalCarrierParcelNumbersDetails' => [
                        'EXT-' . $parcelId => [
                            'externalCarrierId' => 1,
                            'externalCarrierName' => 'Carrier',
                            'trackAndTraceUrl' => 'https://carrier.example/' . $parcelId,
                        ],
                    ],
                    'operations' => [
                        [
                            'dateTime' => '2026-04-27T10:00:00+03:00',
                            'operationCode' => 175,
                            'description' => 'Predict',
                            'exceptionCodes' => [1007],
                            'additionalInfo' => [
                                'officeURL' => 'https://office.example/PARCEL-1',
                                'recipient' => [
                                    'countryId' => 642,
                                    'phone1' => '+40700111222',
                                    'phone2' => '+40700333444',
                                ],
                                'unsuccessfulDeliveryStickerImageURL' => 'https://stickers.example/PARCEL-1.png',
                                'pickupOfficeType' => 'APT',
                                'geoPUDOId' => 'RO-APT-1',
                                'predict' => [
                                    'predictedVisitDateTimeFrom' => '2026-04-28T09:00:00+03:00',
                                    'predictedVisitDateTimeTo' => '2026-04-28T11:00:00+03:00',
                                    'includedDelayInMinutes' => 15,
                                    'canceled' => false,
                                ],
                            ],
                        ],
                    ],
                ];
            }

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'parcels' => $responseParcels,
            ], JSON_THROW_ON_ERROR));
        });
        $client = $this->makeClient($httpClient);

        $references = [];
        for ($index = 1; $index <= 12; $index++) {
            $references[] = new ShipmentParcelRef('PARCEL-' . $index, null, null);
        }

        $response = $client->tracking()->trackBatched($references, true);
        $firstOperation = $response->parcels->first()?->operations->first();

        $this->assertSame([10, 2], $payloadSizes);
        $this->assertCount(12, $response->parcels);
        $this->assertSame('Predict', $firstOperation?->resolvedOperationCode()->description);
        $this->assertSame('New date scheduled', $firstOperation?->resolvedExceptionCodes()[0]->description ?? null);
        $this->assertSame('EXT-PARCEL-1', $response->parcels->first()?->externalCarrierParcelNumbers->first());
        $this->assertSame('https://office.example/PARCEL-1', $firstOperation?->additionalInfo?->officeUrl);
        $this->assertSame(642, $firstOperation?->additionalInfo?->recipient?->countryId);
        $this->assertSame('https://stickers.example/PARCEL-1.png', $firstOperation?->additionalInfo?->unsuccessfulDeliveryStickerImageUrl);
        $this->assertSame(OfficeType::Apt, $firstOperation?->additionalInfo?->pickupOfficeType);
        $this->assertSame('RO-APT-1', $firstOperation?->additionalInfo?->geoPUDOId);
    }

    public function testBulkTrackingFilesExposeNextProcessedFileId(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'files' => [
                ['id' => 7, 'url' => 'https://bulk.example/7.json'],
                ['id' => 8, 'url' => 'https://bulk.example/8.json'],
            ],
        ], JSON_THROW_ON_ERROR)));
        $client = $this->makeClient($httpClient);

        $response = $client->tracking()->bulkDataFiles(5);

        $this->assertSame('/v1/track/bulk', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame(8, $response->nextLastProcessedFileId());
        $this->assertSame('https://bulk.example/7.json', $response->files->first()?->url);
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
