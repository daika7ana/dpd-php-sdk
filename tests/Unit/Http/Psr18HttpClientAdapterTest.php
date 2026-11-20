<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Http;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\Exceptions\TransportException;
use Daika7ana\Dpd\Http\Psr18HttpClientAdapter;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Psr18HttpClientAdapterTest extends TestCase
{
    public function testDelegatesToAnyPsr18Client(): void
    {
        $inner = new class implements ClientInterface {
            public ?RequestInterface $lastRequest = null;

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->lastRequest = $request;

                return new Response(200, [], 'ok');
            }
        };

        $request = new Request('GET', 'https://example.test/ping');

        $response = (new Psr18HttpClientAdapter($inner))->sendRequest($request);

        $this->assertSame($request, $inner->lastRequest);
        $this->assertSame('ok', (string) $response->getBody());
    }

    public function testRejectsPerCallTimeout(): void
    {
        $inner = new class implements ClientInterface {
            public bool $called = false;

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->called = true;

                return new Response(200);
            }
        };

        try {
            (new Psr18HttpClientAdapter($inner))->sendRequest(new Request('GET', 'https://example.test/ping'), 30.0);
            $this->fail('Expected InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('A foreign PSR-18 client cannot honour the SDK per-call timeout; configure the timeout on that client.', $e->getMessage());
        }

        $this->assertFalse($inner->called);
    }

    public function testWrapsPsr18ClientExceptionAsTransportException(): void
    {
        $request = new Request('GET', 'https://example.test/ping');
        $inner = new class ($request) implements ClientInterface {
            public function __construct(private RequestInterface $request) {}

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new ConnectException('Connection refused', $this->request);
            }
        };

        try {
            (new Psr18HttpClientAdapter($inner))->sendRequest($request);
            $this->fail('Expected TransportException.');
        } catch (TransportException $e) {
            $this->assertInstanceOf(ConnectException::class, $e->getPrevious());
        }
    }

    public function testClientAcceptsBarePsr18Client(): void
    {
        $bare = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new Response(200);
            }
        };

        $factory = new HttpFactory();

        $client = Client::create(
            httpClient: $bare,
            requestFactory: $factory,
            streamFactory: $factory,
            config: new ClientConfig(username: 'demo-user', password: 'demo-password', language: 'EN', clientSystemId: 55),
        );

        $this->assertInstanceOf(Client::class, $client);
    }
}
