<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentAdditionalServices
{
    public function __construct(
        public ?ShipmentCodAdditionalService $cod = null,
        public ?ShipmentObpd $obpd = null,
        public ?ShipmentDeclaredValueAdditionalService $declaredValue = null,
        public ?int $fixedTimeDelivery = null,
        public ?ShipmentReturnAdditionalServices $returns = null,
        public ?int $specialDeliveryId = null,
        public ?int $deliveryToFloor = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|int>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'cod' => $this->cod?->toArray(),
            'obpd' => $this->obpd?->toArray(),
            'declaredValue' => $this->declaredValue?->toArray(),
            'fixedTimeDelivery' => $this->fixedTimeDelivery,
            'returns' => $this->returns?->toArray(),
            'specialDeliveryId' => $this->specialDeliveryId,
            'deliveryToFloor' => $this->deliveryToFloor,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            cod: isset($data['cod']) && is_array($data['cod']) ? ShipmentCodAdditionalService::fromArray($data['cod']) : null,
            obpd: isset($data['obpd']) && is_array($data['obpd']) ? ShipmentObpd::fromArray($data['obpd']) : null,
            declaredValue: isset($data['declaredValue']) && is_array($data['declaredValue']) ? ShipmentDeclaredValueAdditionalService::fromArray($data['declaredValue']) : null,
            fixedTimeDelivery: isset($data['fixedTimeDelivery']) ? (int) $data['fixedTimeDelivery'] : null,
            returns: isset($data['returns']) && is_array($data['returns']) ? ShipmentReturnAdditionalServices::fromArray($data['returns']) : null,
            specialDeliveryId: isset($data['specialDeliveryId']) ? (int) $data['specialDeliveryId'] : null,
            deliveryToFloor: isset($data['deliveryToFloor']) ? (int) $data['deliveryToFloor'] : null,
        );
    }
}
