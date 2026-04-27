<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Clients\Contracts;

final readonly class CodInternationalAnnexContractInfo
{
    public function __construct(
        public int $countryId,
        public string $countryName,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryId: (int) ($data['countryId'] ?? 0),
            countryName: (string) ($data['countryName'] ?? ''),
        );
    }
}
