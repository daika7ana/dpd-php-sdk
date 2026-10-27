<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentReturnVoucherAdditionalService
{
    public function __construct(
        public int $serviceId,
        public Payer $payer,
        public ?int $validityPeriod = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'serviceId' => $this->serviceId,
            'payer' => $this->payer->value,
            'validityPeriod' => $this->validityPeriod,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            serviceId: (int) ($data['serviceId'] ?? 0),
            payer: Payer::tryFrom((string) ($data['payer'] ?? '')) ?? Payer::Sender,
            validityPeriod: isset($data['validityPeriod']) ? (int) $data['validityPeriod'] : null,
        );
    }
}
