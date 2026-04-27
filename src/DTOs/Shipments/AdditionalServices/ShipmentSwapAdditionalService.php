<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentSwapAdditionalService
{
    public function __construct(
        public int $serviceId,
        public int $parcelsCount,
        public ?float $declaredValue = null,
        public ?bool $fragile = null,
        public ?int $returnToOfficeId = null,
        public ?bool $thirdPartyPayer = null,
    ) {}

    /**
     * @return array<string, bool|float|int>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'serviceId' => $this->serviceId,
            'parcelsCount' => $this->parcelsCount,
            'declaredValue' => $this->declaredValue,
            'fragile' => $this->fragile,
            'returnToOfficeId' => $this->returnToOfficeId,
            'thirdPartyPayer' => $this->thirdPartyPayer,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            serviceId: (int) ($data['serviceId'] ?? 0),
            parcelsCount: (int) ($data['parcelsCount'] ?? 0),
            declaredValue: isset($data['declaredValue']) ? (float) $data['declaredValue'] : null,
            fragile: isset($data['fragile']) ? (bool) $data['fragile'] : null,
            returnToOfficeId: isset($data['returnToOfficeId']) ? (int) $data['returnToOfficeId'] : null,
            thirdPartyPayer: isset($data['thirdPartyPayer']) ? (bool) $data['thirdPartyPayer'] : null,
        );
    }
}
