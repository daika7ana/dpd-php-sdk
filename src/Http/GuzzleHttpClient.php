<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Http;

use Daika7ana\Dpd\Exceptions\TransportException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class GuzzleHttpClient implements HttpClientInterface
{
    private GuzzleClient $guzzleClient;

    public function __construct(
        ?GuzzleClient $guzzleClient = null,
        ?float $timeout = null,
        ?float $connectTimeout = null,
    ) {
        $this->guzzleClient = $guzzleClient ?? new GuzzleClient(array_filter([
            'timeout' => $timeout,
            'connect_timeout' => $connectTimeout,
        ], static fn(mixed $value): bool => $value !== null));
    }

    public function sendRequest(RequestInterface $request, ?float $timeout = null): ResponseInterface
    {
        try {
            if ($timeout !== null) {
                return $this->guzzleClient->send($request, ['timeout' => $timeout]);
            }

            return $this->guzzleClient->sendRequest($request);
        } catch (GuzzleException $exception) {
            throw new TransportException(
                message: $exception->getMessage(),
                code: $exception->getCode(),
                previous: $exception,
            );
        }
    }
}
