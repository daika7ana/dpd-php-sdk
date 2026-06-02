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
use GuzzleHttp\Psr7\Response;
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

    public function testConstructorAcceptsTimeoutOptions(): void
    {
        // Should not throw when valid timeout options are provided
        $mockHandler = new MockHandler([new Response(200)]);
        $guzzle = new GuzzleClient([
            'handler' => HandlerStack::create($mockHandler),
            'timeout' => 30.0,
            'connect_timeout' => 5.0,
        ]);
        $client = new GuzzleHttpClient($guzzle);

        $response = $client->sendRequest(new Request('GET', 'https://api.dpd.ro/v1/test'));

        $this->assertSame(200, $response->getStatusCode());
    }
}
