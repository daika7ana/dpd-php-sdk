<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\Enums\Services\AdditionalServiceAllowance;
use Daika7ana\Dpd\Enums\Services\CargoType;
use Daika7ana\Dpd\Exceptions\ValidationException;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use DateTimeImmutable;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ServicesResourceTest extends TestCase
{
    public function testListMapsTypedServicesAndUsesSharedPipeline(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'services' => [
                [
                    'id' => 2505,
                    'name' => 'Courier',
                    'nameEn' => 'Courier',
                    'cargoType' => 'PARCEL',
                    'requireParcelWeight' => true,
                    'requireParcelSize' => false,
                    'additionalServices' => [
                        'cod' => ['allowance' => 'ALLOWED'],
                        'declaredValue' => ['allowance' => 'REQUIRED'],
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR)));
        $factory = new HttpFactory();

        $client = Client::create(
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

        $services = $client->services()->list(new DateTimeImmutable('2026-04-27'));
        $payload = JsonHelper::decode((string) $httpClient->lastRequest?->getBody());
        $service = $services->first();

        $this->assertNotNull($service);
        $this->assertSame('/v1/services', $httpClient->lastRequest?->getUri()->getPath());
        $this->assertSame('demo-user', $payload['userName']);
        $this->assertSame('demo-password', $payload['password']);
        $this->assertSame('EN', $payload['language']);
        $this->assertSame(55, $payload['clientSystemId']);
        $this->assertSame('2026-04-27', $payload['date']);
        $this->assertSame(2505, $service->id);
        $this->assertSame(CargoType::Parcel, $service->cargoType);
        $this->assertSame(AdditionalServiceAllowance::Allowed, $service->additionalServices->cod?->allowance);
        $this->assertSame(AdditionalServiceAllowance::Required, $service->additionalServices->declaredValue?->allowance);
    }

    public function testListThrowsTypedValidationExceptionForEmbeddedApiErrors(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'error' => [
                'code' => 120,
                'message' => 'Invalid address.',
                'id' => 'err-120',
                'context' => 'recipient',
                'component' => '$.recipient.address.siteId',
            ],
        ], JSON_THROW_ON_ERROR)));
        $factory = new HttpFactory();

        $client = Client::create(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(username: 'demo-user', password: 'demo-password'),
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid address.');

        $client->services()->list();
    }
}
