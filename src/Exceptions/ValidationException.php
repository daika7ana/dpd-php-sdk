<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Exceptions;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationException extends DpdException
{
    /**
     * @param array<string, string[]> $errors
     */
    public function __construct(
        public readonly array $errors = [],
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public readonly ?ResponseInterface $response = null,
        public readonly ?int $retryAfter = null,
        public readonly ?DpdError $error = null,
    ) {
        parent::__construct($message ?: 'Validation failed', $response?->getStatusCode() ?? $code, $previous);
    }
}
