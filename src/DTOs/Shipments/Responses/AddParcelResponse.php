<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

final readonly class AddParcelResponse
{
    public function __construct(
        public ?CreatedShipmentParcel $parcel = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            parcel: isset($data['parcel']) && is_array($data['parcel']) ? CreatedShipmentParcel::fromArray($data['parcel']) : null,
        );
    }
}
