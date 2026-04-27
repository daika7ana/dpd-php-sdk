<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

final readonly class CodPayment
{
    public function __construct(
        public ?string $date = null,
        public ?float $totalPayedOutAmount = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            date: isset($data['date']) ? (string) $data['date'] : null,
            totalPayedOutAmount: isset($data['totalPayedOutAmount']) ? (float) $data['totalPayedOutAmount'] : null,
        );
    }
}
