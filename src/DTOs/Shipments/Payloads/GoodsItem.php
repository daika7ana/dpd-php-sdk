<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

final readonly class GoodsItem
{
    public function __construct(
        public string $description,
        public string $hsCode,
        public int $quantity,
        public float $valuePerItem,
        public float $weightPerItem,
        public string $originCountryCode,
    ) {}

    /**
     * @return array<string, float|int|string>
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'hsCode' => $this->hsCode,
            'quantity' => $this->quantity,
            'valuePerItem' => $this->valuePerItem,
            'weightPerItem' => $this->weightPerItem,
            'originCountryCode' => $this->originCountryCode,
        ];
    }
}
