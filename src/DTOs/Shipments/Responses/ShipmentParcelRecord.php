<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelSize;

final readonly class ShipmentParcelRecord
{
    /**
     * @param list<string> $externalCarrierParcelNumbers
     */
    public function __construct(
        public string $id,
        public int $seqNo,
        public ?int $packageUniqueNumber = null,
        public ?ShipmentParcelSize $declaredSize = null,
        public ?ShipmentParcelSize $measuredSize = null,
        public ?ShipmentParcelSize $calculationSize = null,
        public ?float $declaredWeight = null,
        public ?float $measuredWeight = null,
        public ?float $calculationWeight = null,
        public array $externalCarrierParcelNumbers = [],
        public ?string $baseType = null,
        public ?string $sizeName = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var list<string> $externalCarrierParcelNumbers */
        $externalCarrierParcelNumbers = array_values(array_map('strval', $data['externalCarrierParcelNumbers'] ?? []));

        return new self(
            id: (string) ($data['id'] ?? ''),
            seqNo: (int) ($data['seqNo'] ?? 0),
            packageUniqueNumber: isset($data['packageUniqueNumber']) ? (int) $data['packageUniqueNumber'] : null,
            declaredSize: isset($data['declaredSize']) && is_array($data['declaredSize']) ? ShipmentParcelSize::fromArray($data['declaredSize']) : null,
            measuredSize: isset($data['measuredSize']) && is_array($data['measuredSize']) ? ShipmentParcelSize::fromArray($data['measuredSize']) : null,
            calculationSize: isset($data['calculationSize']) && is_array($data['calculationSize']) ? ShipmentParcelSize::fromArray($data['calculationSize']) : null,
            declaredWeight: isset($data['declaredWeight']) ? (float) $data['declaredWeight'] : null,
            measuredWeight: isset($data['measuredWeight']) ? (float) $data['measuredWeight'] : null,
            calculationWeight: isset($data['calculationWeight']) ? (float) $data['calculationWeight'] : null,
            externalCarrierParcelNumbers: $externalCarrierParcelNumbers,
            baseType: isset($data['baseType']) ? (string) $data['baseType'] : null,
            sizeName: isset($data['size']) ? (string) $data['size'] : null,
        );
    }
}
