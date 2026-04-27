<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Validation\Responses;

final readonly class ValidationResult
{
    public function __construct(
        public bool $valid,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self((bool) ($data['valid'] ?? false));
    }
}
