<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Auth\Credentials;
use Daika7ana\Dpd\Auth\InMemoryCredentialsProvider;
use Daika7ana\Dpd\Cache\ArrayReferenceDataCache;
use Daika7ana\Dpd\Cache\NullReferenceDataCache;
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\Http\ApiRequest;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientExtensionSeamsTest extends TestCase
{
    public function testCustomCredentialsProviderCanRotateCredentialsAfterClientConstruction(): void
    {
        $credentialsProvider = new InMemoryCredentialsProvider(new Credentials('first-user', 'first-password', 'RO', 1));
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], '{}'));
        $factory = new HttpFactory();
        $client = Client::create(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(),
            credentialsProvider: $credentialsProvider,
        );

        $credentialsProvider->setCredentials(new Credentials('second-user', 'second-password', 'EN', 22));
        $request = $client->createRequest(ApiRequest::json('POST', '/services'));
        $payload = JsonHelper::decode((string) $request->getBody());

        $this->assertSame('second-user', $payload['userName']);
        $this->assertSame('second-password', $payload['password']);
        $this->assertSame('EN', $payload['language']);
        $this->assertSame(22, $payload['clientSystemId']);
    }

    public function testArrayReferenceDataCacheAvoidsRepeatedResolution(): void
    {
        $factory = new HttpFactory();
        $client = Client::create(
            httpClient: new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], '{}')),
            requestFactory: $factory,
            streamFactory: $factory,
            referenceDataCache: new ArrayReferenceDataCache(),
        );

        $calls = 0;
        $first = $client->rememberReferenceData('countries:ro', static function () use (&$calls): array {
            $calls++;

            return ['RO', 'BG'];
        });
        $second = $client->rememberReferenceData('countries:ro', static function () use (&$calls): array {
            $calls++;

            return ['ignored'];
        });

        $this->assertSame(['RO', 'BG'], $first);
        $this->assertSame(['RO', 'BG'], $second);
        $this->assertSame(1, $calls);
    }

    public function testNullReferenceDataCacheRemainsPassThrough(): void
    {
        $factory = new HttpFactory();
        $client = Client::create(
            httpClient: new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], '{}')),
            requestFactory: $factory,
            streamFactory: $factory,
            referenceDataCache: new NullReferenceDataCache(),
        );

        $calls = 0;
        $client->rememberReferenceData('sites:642', static function () use (&$calls): array {
            $calls++;

            return [1, 2, 3];
        });
        $client->rememberReferenceData('sites:642', static function () use (&$calls): array {
            $calls++;

            return [4, 5, 6];
        });

        $this->assertSame(2, $calls);
    }
}
