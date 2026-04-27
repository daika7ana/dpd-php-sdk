<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Clients\Requests;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\ShipmentAddressPayload;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class CreateContactRequest
{
    public function __construct(
        public string $externalContactId,
        public ShipmentPhoneNumber $phone1,
        public string $clientName,
        public bool $privatePerson,
        public ShipmentAddressPayload $address,
        public ?string $secretKey = null,
        public ?ShipmentPhoneNumber $phone2 = null,
        public ?string $objectName = null,
        public ?string $contactName = null,
        public ?string $email = null,
    ) {}

    /**
     * @return array<string, array<string, float|int|string>|bool|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'externalContactId' => $this->externalContactId,
            'secretKey' => $this->secretKey,
            'phone1' => $this->phone1->toArray(),
            'phone2' => $this->phone2?->toArray(),
            'clientName' => $this->clientName,
            'objectName' => $this->objectName,
            'contactName' => $this->contactName,
            'email' => $this->email,
            'privatePerson' => $this->privatePerson,
            'address' => $this->address->toArray(),
        ]);
    }
}
