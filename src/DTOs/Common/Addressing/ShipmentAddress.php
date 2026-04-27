<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Addressing;

final readonly class ShipmentAddress
{
    public function __construct(
        public AddressLocation $location,
        public ?int $streetId,
        public ?string $streetType,
        public ?string $streetName,
        public ?string $streetNo,
        public ?int $complexId,
        public ?string $complexType,
        public ?string $complexName,
        public ?string $blockNo,
        public ?string $entranceNo,
        public ?string $floorNo,
        public ?string $apartmentNo,
        public ?int $poiId,
        public ?string $addressNote,
        public ?string $addressLine1,
        public ?string $addressLine2,
        public ?string $fullAddressString = null,
        public ?string $siteAddressString = null,
        public ?string $localAddressString = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            location: AddressLocation::fromArray($data),
            streetId: isset($data['streetId']) ? (int) $data['streetId'] : null,
            streetType: isset($data['streetType']) ? (string) $data['streetType'] : null,
            streetName: isset($data['streetName']) ? (string) $data['streetName'] : null,
            streetNo: isset($data['streetNo']) ? (string) $data['streetNo'] : null,
            complexId: isset($data['complexId']) ? (int) $data['complexId'] : null,
            complexType: isset($data['complexType']) ? (string) $data['complexType'] : null,
            complexName: isset($data['complexName']) ? (string) $data['complexName'] : null,
            blockNo: isset($data['blockNo']) ? (string) $data['blockNo'] : null,
            entranceNo: isset($data['entranceNo']) ? (string) $data['entranceNo'] : null,
            floorNo: isset($data['floorNo']) ? (string) $data['floorNo'] : null,
            apartmentNo: isset($data['apartmentNo']) ? (string) $data['apartmentNo'] : null,
            poiId: isset($data['poiId']) ? (int) $data['poiId'] : null,
            addressNote: isset($data['addressNote']) ? (string) $data['addressNote'] : null,
            addressLine1: isset($data['addressLine1']) ? (string) $data['addressLine1'] : null,
            addressLine2: isset($data['addressLine2']) ? (string) $data['addressLine2'] : null,
            fullAddressString: isset($data['fullAddressString']) ? (string) $data['fullAddressString'] : null,
            siteAddressString: isset($data['siteAddressString']) ? (string) $data['siteAddressString'] : null,
            localAddressString: isset($data['localAddressString']) ? (string) $data['localAddressString'] : null,
        );
    }

    public function usesLocalAddressing(): bool
    {
        return $this->streetId !== null
            || $this->streetType !== null
            || $this->streetName !== null
            || $this->complexId !== null
            || $this->complexType !== null
            || $this->complexName !== null
            || $this->blockNo !== null
            || $this->poiId !== null
            || $this->addressNote !== null;
    }

    public function usesForeignAddressing(): bool
    {
        return $this->addressLine1 !== null
            || $this->addressLine2 !== null
            || $this->location->stateId !== null;
    }
}
