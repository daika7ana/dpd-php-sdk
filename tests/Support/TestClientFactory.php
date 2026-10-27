<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Support;

use Daika7ana\Dpd\Cache\ReferenceDataCacheInterface;
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use GuzzleHttp\Psr7\HttpFactory;

final class TestClientFactory
{
    public static function create(FakeHttpClient $httpClient, ?ReferenceDataCacheInterface $cache = null): Client
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
            referenceDataCache: $cache,
        );
    }
}
