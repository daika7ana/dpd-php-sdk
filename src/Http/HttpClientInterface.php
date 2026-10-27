<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface extends ClientInterface
{
    /**
     * @param float|null $timeout Per-request timeout in seconds; null uses the HTTP client default.
     */
    public function sendRequest(RequestInterface $request, ?float $timeout = null): ResponseInterface;
}
