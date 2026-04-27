<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Clients\Records;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentAddress;

final readonly class ClientRecord
{
    public function __construct(
        public int $clientId,
        public string $clientName,
        public ?string $objectName = null,
        public ?string $contactName = null,
        public ?ShipmentAddress $address = null,
        public ?string $email = null,
        public ?bool $privatePerson = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            clientId: (int) ($data['clientId'] ?? 0),
            clientName: (string) ($data['clientName'] ?? ''),
            objectName: isset($data['objectName']) ? (string) $data['objectName'] : null,
            contactName: isset($data['contactName']) ? (string) $data['contactName'] : null,
            address: is_array($data['address'] ?? null) ? ShipmentAddress::fromArray($data['address']) : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            privatePerson: isset($data['privatePerson']) ? (bool) $data['privatePerson'] : null,
        );
    }
}
