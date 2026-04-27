<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

final readonly class TrackedParcelOperationAdditionalInfoRecipient
{
    public function __construct(
        public ?int $countryId = null,
        public ?string $phone1 = null,
        public ?string $phone2 = null,
        public ?string $phone3 = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryId: isset($data['countryId']) ? (int) $data['countryId'] : null,
            phone1: isset($data['phone1']) ? (string) $data['phone1'] : null,
            phone2: isset($data['phone2']) ? (string) $data['phone2'] : null,
            phone3: isset($data['phone3']) ? (string) $data['phone3'] : null,
        );
    }
}
