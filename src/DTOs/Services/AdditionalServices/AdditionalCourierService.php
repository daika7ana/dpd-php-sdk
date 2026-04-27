<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Services\AdditionalServices;

use Daika7ana\Dpd\Enums\Services\AdditionalServiceAllowance;

final readonly class AdditionalCourierService
{
    public function __construct(
        public AdditionalServiceAllowance $allowance,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            allowance: AdditionalServiceAllowance::from((string) ($data['allowance'] ?? 'FORBIDDEN')),
        );
    }
}
