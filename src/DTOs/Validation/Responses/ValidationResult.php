<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Validation\Responses;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;

final readonly class ValidationResult
{
    public function __construct(
        public bool $valid,
        public ?DpdError $error = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $error = isset($data['error']) && is_array($data['error']) && $data['error'] !== []
            ? DpdError::fromArray($data['error'])
            : null;

        return new self((bool) ($data['valid'] ?? false), $error);
    }
}
