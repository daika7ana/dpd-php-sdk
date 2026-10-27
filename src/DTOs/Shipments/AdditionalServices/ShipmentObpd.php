<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Shipments\ObpdOption;

final readonly class ShipmentObpd
{
    public function __construct(
        public ObpdOption $option,
        public int $returnShipmentServiceId,
        public Payer $returnShipmentPayer,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'option' => $this->option->value,
            'returnShipmentServiceId' => $this->returnShipmentServiceId,
            'returnShipmentPayer' => $this->returnShipmentPayer->value,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            option: ObpdOption::tryFrom((string) ($data['option'] ?? '')) ?? ObpdOption::Open,
            returnShipmentServiceId: (int) ($data['returnShipmentServiceId'] ?? 0),
            returnShipmentPayer: Payer::tryFrom((string) ($data['returnShipmentPayer'] ?? '')) ?? Payer::Sender,
        );
    }
}
