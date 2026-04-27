<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Support;

final readonly class ShipmentPriceAmount
{
    public function __construct(
        public float $amount,
        public ?float $percent,
        public float $vatPercent,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) ($data['amount'] ?? 0.0),
            percent: isset($data['percent']) ? (float) $data['percent'] : null,
            vatPercent: (float) ($data['vatPercent'] ?? 0.0),
        );
    }
}
