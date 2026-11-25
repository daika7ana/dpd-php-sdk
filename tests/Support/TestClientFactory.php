<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Support;

use Daika7ana\Dpd\Cache\ReferenceDataCacheInterface;
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class TestClientFactory
{
    /**
     * @param callable(RequestInterface): ResponseInterface $handler
     * @return array{Client, FakeHttpClient}
     */
    public static function create(callable $handler, ?ClientConfig $config = null, ?ReferenceDataCacheInterface $cache = null): array
    {
        $httpClient = new FakeHttpClient($handler);
        $factory = new HttpFactory();

        $client = Client::create(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            config: $config ?? new ClientConfig(
                username: 'demo-user',
                password: 'demo-password',
                language: 'EN',
                clientSystemId: 55,
            ),
            referenceDataCache: $cache,
        );

        return [$client, $httpClient];
    }
}
