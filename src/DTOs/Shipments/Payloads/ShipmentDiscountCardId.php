<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

final readonly class ShipmentDiscountCardId
{
    public function __construct(
        public int $contractId,
        public int $cardId,
    ) {}

    /**
     * @return array{contractId: int, cardId: int}
     */
    public function toArray(): array
    {
        return [
            'contractId' => $this->contractId,
            'cardId' => $this->cardId,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            contractId: (int) ($data['contractId'] ?? 0),
            cardId: (int) ($data['cardId'] ?? 0),
        );
    }
}
