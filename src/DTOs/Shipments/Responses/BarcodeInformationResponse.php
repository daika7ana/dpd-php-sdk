<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Documents\LabelInfo;

final readonly class BarcodeInformationResponse
{
    public function __construct(
        public ?LabelInfo $labelInfo = null,
        public ?PrimaryShipment $primaryShipment = null,
        public ?string $primaryParcelId = null,
        public ?string $returnShipmentId = null,
        public ?string $returnParcelId = null,
        public ?string $redirectShipmentId = null,
        public ?string $redirectParcelId = null,
        public ?string $initialShipmentId = null,
        public ?string $initialParcelId = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            labelInfo: isset($data['labelInfo']) && is_array($data['labelInfo']) ? LabelInfo::fromArray($data['labelInfo']) : null,
            primaryShipment: isset($data['primaryShipment']) && is_array($data['primaryShipment']) ? PrimaryShipment::fromArray($data['primaryShipment']) : null,
            primaryParcelId: isset($data['primaryParcelId']) ? (string) $data['primaryParcelId'] : null,
            returnShipmentId: isset($data['returnShipmentId']) ? (string) $data['returnShipmentId'] : null,
            returnParcelId: isset($data['returnParcelId']) ? (string) $data['returnParcelId'] : null,
            redirectShipmentId: isset($data['redirectShipmentId']) ? (string) $data['redirectShipmentId'] : null,
            redirectParcelId: isset($data['redirectParcelId']) ? (string) $data['redirectParcelId'] : null,
            initialShipmentId: isset($data['initialShipmentId']) ? (string) $data['initialShipmentId'] : null,
            initialParcelId: isset($data['initialParcelId']) ? (string) $data['initialParcelId'] : null,
        );
    }
}
