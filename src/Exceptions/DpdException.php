<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Exceptions;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use RuntimeException;
use Throwable;

class DpdException extends RuntimeException
{
    /**
     * @param array<string, mixed> $responseDetails
     */
    public function __construct(
        string $message,
        int $code = 0,
        public readonly ?DpdError $error = null,
        public readonly array $responseDetails = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
