<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Parties;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\ShipmentAddressPayload;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentRecipient
{
    public function __construct(
        public ?ShipmentPhoneNumber $phone1 = null,
        public ?int $clientId = null,
        public ?ShipmentPhoneNumber $phone2 = null,
        public ?ShipmentPhoneNumber $phone3 = null,
        public ?string $clientName = null,
        public ?string $objectName = null,
        public ?string $contactName = null,
        public ?string $email = null,
        public ?bool $privatePerson = null,
        public ?ShipmentAddressPayload $address = null,
        public ?int $pickupOfficeId = null,
        public ?string $pickupGeoPUDOId = null,
        public ?bool $autoSelectNearestOffice = null,
        public ?AutoSelectNearestOfficePolicy $autoSelectNearestOfficePolicy = null,
    ) {}

    /**
     * @return array<string, array<string, float|int|string>|bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'clientId' => $this->clientId,
            'phone1' => $this->phone1?->toArray(),
            'phone2' => $this->phone2?->toArray(),
            'phone3' => $this->phone3?->toArray(),
            'clientName' => $this->clientName,
            'objectName' => $this->objectName,
            'contactName' => $this->contactName,
            'email' => $this->email,
            'privatePerson' => $this->privatePerson,
            'address' => $this->address?->toArray(),
            'pickupOfficeId' => $this->pickupOfficeId,
            'pickupGeoPUDOId' => $this->pickupGeoPUDOId,
            'autoSelectNearestOffice' => $this->autoSelectNearestOffice,
            'autoSelectNearestOfficePolicy' => $this->autoSelectNearestOfficePolicy?->toArray(),
        ]);
    }
}
