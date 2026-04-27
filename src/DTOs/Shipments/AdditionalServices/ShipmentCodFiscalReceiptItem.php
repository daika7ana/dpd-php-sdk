<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

final readonly class ShipmentCodFiscalReceiptItem
{
    public function __construct(
        public string $description,
        public string $vatGroup,
        public float $amount,
        public float $amountWithVat,
    ) {}

    /**
     * @return array<string, float|string>
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'vatGroup' => $this->vatGroup,
            'amount' => $this->amount,
            'amountWithVat' => $this->amountWithVat,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            description: (string) ($data['description'] ?? ''),
            vatGroup: (string) ($data['vatGroup'] ?? ''),
            amount: (float) ($data['amount'] ?? 0.0),
            amountWithVat: (float) ($data['amountWithVat'] ?? 0.0),
        );
    }
}
