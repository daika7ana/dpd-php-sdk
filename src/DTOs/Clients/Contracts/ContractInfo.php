<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Clients\Contracts;

final readonly class ContractInfo
{
    public function __construct(
        public int $id,
        public bool $administrativeFeeAllowed,
        public ?SpecialDeliveryRequirements $specialDeliveryRequirements = null,
        public ?CodAdditionalServiceContractInfo $cod = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            administrativeFeeAllowed: (bool) ($data['administrativeFeeAllowed'] ?? false),
            specialDeliveryRequirements: is_array($data['specialDeliveryRequirements'] ?? null)
                ? SpecialDeliveryRequirements::fromArray($data['specialDeliveryRequirements'])
                : null,
            cod: is_array($data['cod'] ?? null) ? CodAdditionalServiceContractInfo::fromArray($data['cod']) : null,
        );
    }
}
