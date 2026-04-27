<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Clients\Contracts;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class SpecialDeliveryRequirements
{
    /**
     * @param Collection<int, Requirement> $requirements
     */
    public function __construct(
        public bool $requiredForAllShipments,
        public Collection $requirements,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $requirements = array_map(
            static fn(array $requirement): Requirement => Requirement::fromArray($requirement),
            array_values(array_filter($data['requirements'] ?? [], static fn(mixed $requirement): bool => is_array($requirement))),
        );

        return new self(
            requiredForAllShipments: (bool) ($data['requiredForAllShipments'] ?? false),
            requirements: new Collection($requirements),
        );
    }
}
