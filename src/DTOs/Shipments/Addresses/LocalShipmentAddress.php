<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Addresses;

use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class LocalShipmentAddress implements ShipmentAddressPayload
{
    public function __construct(
        public AddressLocation $location,
        public ?int $streetId = null,
        public ?string $streetType = null,
        public ?string $streetName = null,
        public ?string $streetNo = null,
        public ?int $complexId = null,
        public ?string $complexType = null,
        public ?string $complexName = null,
        public ?string $blockNo = null,
        public ?string $entranceNo = null,
        public ?string $floorNo = null,
        public ?string $apartmentNo = null,
        public ?int $poiId = null,
        public ?string $addressNote = null,
    ) {}

    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            ...$this->location->toArray(),
            'streetId' => $this->streetId,
            'streetType' => $this->streetType,
            'streetName' => $this->streetName,
            'streetNo' => $this->streetNo,
            'complexId' => $this->complexId,
            'complexType' => $this->complexType,
            'complexName' => $this->complexName,
            'blockNo' => $this->blockNo,
            'entranceNo' => $this->entranceNo,
            'floorNo' => $this->floorNo,
            'apartmentNo' => $this->apartmentNo,
            'poiId' => $this->poiId,
            'addressNote' => $this->addressNote,
        ]);
    }
}
