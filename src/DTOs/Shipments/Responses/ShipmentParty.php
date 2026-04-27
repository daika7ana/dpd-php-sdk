<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentAddress;

final readonly class ShipmentParty
{
    public function __construct(
        public ?int $clientId = null,
        public ?string $clientName = null,
        public ?string $objectName = null,
        public ?string $contactName = null,
        public ?ShipmentAddress $address = null,
        public ?string $email = null,
        public ?bool $privatePerson = null,
        public ?int $dropoffOfficeId = null,
        public ?string $dropoffGeoPUDOId = null,
        public ?int $pickupOfficeId = null,
        public ?string $pickupGeoPUDOId = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            clientId: isset($data['clientId']) ? (int) $data['clientId'] : null,
            clientName: isset($data['clientName']) ? (string) $data['clientName'] : null,
            objectName: isset($data['objectName']) ? (string) $data['objectName'] : null,
            contactName: isset($data['contactName']) ? (string) $data['contactName'] : null,
            address: isset($data['address']) && is_array($data['address']) ? ShipmentAddress::fromArray($data['address']) : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            privatePerson: isset($data['privatePerson']) ? (bool) $data['privatePerson'] : null,
            dropoffOfficeId: isset($data['dropoffOfficeId']) ? (int) $data['dropoffOfficeId'] : null,
            dropoffGeoPUDOId: isset($data['dropoffGeoPUDOId']) ? (string) $data['dropoffGeoPUDOId'] : null,
            pickupOfficeId: isset($data['pickupOfficeId']) ? (int) $data['pickupOfficeId'] : null,
            pickupGeoPUDOId: isset($data['pickupGeoPUDOId']) ? (string) $data['pickupGeoPUDOId'] : null,
        );
    }
}
