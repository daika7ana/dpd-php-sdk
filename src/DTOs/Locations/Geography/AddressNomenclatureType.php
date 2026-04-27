<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class AddressNomenclatureType
{
    public function __construct(
        public BilingualText $name,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
        );
    }

    public static function fromCsvValues(?string $local, ?string $english): self
    {
        return new self(BilingualText::fromValues($local, $english));
    }
}
