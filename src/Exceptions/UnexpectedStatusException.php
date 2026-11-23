<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Exceptions;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Psr\Http\Message\ResponseInterface;

class UnexpectedStatusException extends DpdException
{
    /**
     * @param array<string, mixed> $responseDetails
     */
    public function __construct(
        public readonly ?ResponseInterface $response = null,
        string $message = '',
        int $code = 0,
        public readonly ?int $retryAfter = null,
        public readonly ?DpdError $error = null,
        public readonly array $responseDetails = [],
    ) {
        parent::__construct($message !== '' ? $message : self::defaultMessage($response), $response?->getStatusCode() ?? $code);
    }

    private static function defaultMessage(?ResponseInterface $response): string
    {
        return $response === null ? 'Unexpected HTTP status' : 'Unexpected HTTP status ' . $response->getStatusCode();
    }
}
