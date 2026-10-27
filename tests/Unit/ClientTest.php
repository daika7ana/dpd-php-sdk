<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\Http\ApiRequest;
use Daika7ana\Dpd\Support\JsonHelper;
use Daika7ana\Dpd\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testFromEnvironmentUsesEnvironmentValues(): void
    {
        putenv('DPD_BASE_URL=https://example.test/v1');
        putenv('DPD_USERNAME=demo');
        putenv('DPD_PASSWORD=secret');
        putenv('DPD_LANGUAGE=en');
        putenv('DPD_CLIENT_SYSTEM_ID=42');

        $config = ClientConfig::fromEnvironment();

        $this->assertSame('https://example.test/v1', $config->baseUrl);
        $this->assertSame('demo', $config->username);
        $this->assertSame('secret', $config->password);
        $this->assertSame('EN', $config->language);
        $this->assertSame(42, $config->clientSystemId);

        putenv('DPD_BASE_URL');
        putenv('DPD_USERNAME');
        putenv('DPD_PASSWORD');
        putenv('DPD_LANGUAGE');
        putenv('DPD_CLIENT_SYSTEM_ID');
    }

    public function testCreateRequestInjectsCredentialsAndReplacesPathParameters(): void
    {
        $factory = new HttpFactory();
        $client = Client::create(
            httpClient: new FakeHttpClient(static fn() => new Response(200, [], '{}')),
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(
                username: 'demo-user',
                password: 'demo-password',
                language: 'EN',
                clientSystemId: 33,
            ),
        );

        $request = $client->createRequest(ApiRequest::json('POST', '/location/country/{id}', ['name' => 'Romania'], ['id' => 642]));
        $payload = JsonHelper::decode((string) $request->getBody());

        $this->assertSame('/v1/location/country/642', $request->getUri()->getPath());
        $this->assertSame('application/json', $request->getHeaderLine('Accept'));
        $this->assertSame('demo-user', $payload['userName']);
        $this->assertSame('demo-password', $payload['password']);
        $this->assertSame('EN', $payload['language']);
        $this->assertSame(33, $payload['clientSystemId']);
        $this->assertSame('Romania', $payload['name']);
    }

    public function testCustomHttpClientIsUsedWhenSendingRequests(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], '{}'));
        $factory = new HttpFactory();
        $client = Client::create(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(username: 'demo-user', password: 'demo-password'),
        );

        $response = $client->send($client->createRequest(ApiRequest::json('POST', '/services')));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('/v1/services', $httpClient->lastRequest?->getUri()->getPath());
    }

    public function testSendForwardsPerRequestTimeout(): void
    {
        $httpClient = new FakeHttpClient(static fn() => new Response(200, ['Content-Type' => 'application/json'], '{}'));
        $factory = new HttpFactory();
        $client = Client::create(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(username: 'demo-user', password: 'demo-password'),
        );

        $client->send($client->createRequest(ApiRequest::json('POST', '/services')), 2.5);

        $this->assertSame(2.5, $httpClient->lastTimeout);
    }
}
