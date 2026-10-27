<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Support;

use Daika7ana\Dpd\Http\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class FakeHttpClient implements HttpClientInterface
{
    public ?RequestInterface $lastRequest = null;

    public ?float $lastTimeout = null;

    /** @var callable(RequestInterface): ResponseInterface */
    private $handler;

    /**
     * @param callable(RequestInterface): ResponseInterface $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function sendRequest(RequestInterface $request, ?float $timeout = null): ResponseInterface
    {
        $this->lastRequest = $request;
        $this->lastTimeout = $timeout;

        $handler = $this->handler;
        $response = $handler($request);

        if (!$response instanceof ResponseInterface) {
            throw new RuntimeException('FakeHttpClient handler must return a PSR-7 response.');
        }

        return $response;
    }
}
