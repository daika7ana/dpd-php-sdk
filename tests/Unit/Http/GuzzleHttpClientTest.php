<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Http;

use Daika7ana\Dpd\Exceptions\TransportException;
use Daika7ana\Dpd\Http\GuzzleHttpClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;

final class GuzzleHttpClientTest extends TestCase
{
    public function testSendRequestReturnsResponse(): void
    {
        $mockHandler = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $guzzle = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);
        $client = new GuzzleHttpClient($guzzle);

        $response = $client->sendRequest(new Request('POST', 'https://api.dpd.ro/v1/test'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"ok":true}', (string) $response->getBody());
    }

    public function testSendRequestWrapsGuzzleExceptionAsTransportException(): void
    {
        $mockHandler = new MockHandler([
            new ConnectException('Connection timed out', new Request('POST', 'https://api.dpd.ro/v1/test')),
        ]);
        $guzzle = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);
        $client = new GuzzleHttpClient($guzzle);

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Connection timed out');

        $client->sendRequest(new Request('POST', 'https://api.dpd.ro/v1/test'));
    }

    public function testTransportExceptionPreservesOriginalAsPrevious(): void
    {
        $original = new ConnectException('DNS failure', new Request('POST', 'https://api.dpd.ro/v1/test'));
        $mockHandler = new MockHandler([$original]);
        $guzzle = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);
        $client = new GuzzleHttpClient($guzzle);

        try {
            $client->sendRequest(new Request('POST', 'https://api.dpd.ro/v1/test'));
            $this->fail('Expected TransportException');
        } catch (TransportException $e) {
            $this->assertSame($original, $e->getPrevious());
        }
    }

    public function testConstructorAppliesConfiguredTimeouts(): void
    {
        $client = new GuzzleHttpClient(timeout: 1.5, connectTimeout: 0.5);

        $property = new \ReflectionProperty(GuzzleHttpClient::class, 'guzzleClient');
        $config = $property->getValue($client)->getConfig();

        $this->assertSame(1.5, $config['timeout']);
        $this->assertSame(0.5, $config['connect_timeout']);
    }

    public function testPerRequestTimeoutIsForwardedToGuzzle(): void
    {
        $captured = null;
        $guzzle = new GuzzleClient([
            'handler' => static function (RequestInterface $request, array $options) use (&$captured) {
                $captured = $options;

                return Create::promiseFor(new Response(200));
            },
        ]);
        $client = new GuzzleHttpClient($guzzle);

        $client->sendRequest(new Request('GET', 'https://api.dpd.ro/v1/test'), 2.5);

        $this->assertSame(2.5, $captured['timeout'] ?? null);
    }
}
