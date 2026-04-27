<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Results;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentPrice;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentAdditionalServices;

final readonly class CalculationResult
{
    public function __construct(
        public int $serviceId,
        public ?ShipmentAdditionalServices $additionalServices = null,
        public ?ShipmentPrice $price = null,
        public ?string $pickupDate = null,
        public ?string $deliveryDeadline = null,
        public ?DpdError $error = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            serviceId: (int) ($data['serviceId'] ?? 0),
            additionalServices: is_array($data['additionalServices'] ?? null)
                ? ShipmentAdditionalServices::fromArray($data['additionalServices'])
                : null,
            price: is_array($data['price'] ?? null) ? ShipmentPrice::fromArray($data['price']) : null,
            pickupDate: isset($data['pickupDate']) ? (string) $data['pickupDate'] : null,
            deliveryDeadline: isset($data['deliveryDeadline']) ? (string) $data['deliveryDeadline'] : null,
            error: is_array($data['error'] ?? null) ? DpdError::fromArray($data['error']) : null,
        );
    }
}
