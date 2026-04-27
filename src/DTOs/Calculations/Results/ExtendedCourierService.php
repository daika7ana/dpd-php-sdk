<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Results;

use Daika7ana\Dpd\DTOs\Services\AdditionalServices\AdditionalCourierServices;
use Daika7ana\Dpd\Enums\Services\CargoType;

final readonly class ExtendedCourierService
{
    public function __construct(
        public int $id,
        public string $name,
        public string $nameEn,
        public AdditionalCourierServices $additionalServices,
        public CargoType $cargoType,
        public bool $requireParcelWeight,
        public bool $requireParcelSize,
        public ?string $deadline = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            nameEn: (string) ($data['nameEn'] ?? ''),
            additionalServices: AdditionalCourierServices::fromArray(
                is_array($data['additionalServices'] ?? null) ? $data['additionalServices'] : [],
            ),
            cargoType: CargoType::from((string) ($data['cargoType'] ?? 'PARCEL')),
            requireParcelWeight: (bool) ($data['requireParcelWeight'] ?? false),
            requireParcelSize: (bool) ($data['requireParcelSize'] ?? false),
            deadline: isset($data['deadline']) ? (string) $data['deadline'] : null,
        );
    }
}
