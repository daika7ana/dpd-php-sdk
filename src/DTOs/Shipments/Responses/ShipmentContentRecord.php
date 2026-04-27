<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class ShipmentContentRecord
{
    /**
     * @param Collection<int, ShipmentParcelRecord> $parcels
     */
    public function __construct(
        public ?int $parcelsCount = null,
        public ?float $declaredWeight = null,
        public ?float $measuredWeight = null,
        public ?float $calculationWeight = null,
        public ?string $contents = null,
        public ?string $package = null,
        public ?bool $documents = null,
        public ?bool $palletized = null,
        public ?Collection $parcels = null,
        public ?bool $pendingParcels = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $parcels = array_map(
            static fn(array $parcel): ShipmentParcelRecord => ShipmentParcelRecord::fromArray($parcel),
            array_values(array_filter($data['parcels'] ?? [], static fn(mixed $parcel): bool => is_array($parcel))),
        );

        return new self(
            parcelsCount: isset($data['parcelsCount']) ? (int) $data['parcelsCount'] : null,
            declaredWeight: isset($data['declaredWeight']) ? (float) $data['declaredWeight'] : null,
            measuredWeight: isset($data['measuredWeight']) ? (float) $data['measuredWeight'] : null,
            calculationWeight: isset($data['calculationWeight']) ? (float) $data['calculationWeight'] : null,
            contents: isset($data['contents']) ? (string) $data['contents'] : null,
            package: isset($data['package']) ? (string) $data['package'] : null,
            documents: isset($data['documents']) ? (bool) $data['documents'] : null,
            palletized: isset($data['palletized']) ? (bool) $data['palletized'] : null,
            parcels: new Collection($parcels),
            pendingParcels: isset($data['pendingParcels']) ? (bool) $data['pendingParcels'] : null,
        );
    }
}
