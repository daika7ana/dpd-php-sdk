<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Parties;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\ShipmentAddressPayload;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentSender
{
    public function __construct(
        public ShipmentPhoneNumber $phone1,
        public ?int $clientId = null,
        public ?ShipmentPhoneNumber $phone2 = null,
        public ?ShipmentPhoneNumber $phone3 = null,
        public ?string $clientName = null,
        public ?string $contactName = null,
        public ?string $email = null,
        public ?bool $privatePerson = null,
        public ?ShipmentAddressPayload $address = null,
        public ?int $dropoffOfficeId = null,
        public ?string $dropoffGeoPUDOId = null,
    ) {}

    /**
     * @return array<string, array<string, float|int|string>|bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'clientId' => $this->clientId,
            'phone1' => $this->phone1->toArray(),
            'phone2' => $this->phone2?->toArray(),
            'phone3' => $this->phone3?->toArray(),
            'clientName' => $this->clientName,
            'contactName' => $this->contactName,
            'email' => $this->email,
            'privatePerson' => $this->privatePerson,
            'address' => $this->address?->toArray(),
            'dropoffOfficeId' => $this->dropoffOfficeId,
            'dropoffGeoPUDOId' => $this->dropoffGeoPUDOId,
        ]);
    }
}
