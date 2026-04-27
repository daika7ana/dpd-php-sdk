<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Offices;

use Daika7ana\Dpd\DTOs\Locations\Geography\BilingualText;
use Daika7ana\Dpd\DTOs\Common\Routing\RoutingInformation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentAddress;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelSize;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Enums\Services\CargoType;

final readonly class Office
{
    /**
     * @param list<OfficeWorkingTimeSchedule> $workingTimeSchedule
     * @param list<CargoType> $cargoTypesAllowed
     */
    public function __construct(
        public int $id,
        public BilingualText $name,
        public int $siteId,
        public ?ShipmentAddress $address,
        public ?string $workingTimeFrom,
        public ?string $workingTimeTo,
        public ?string $workingTimeHalfFrom,
        public ?string $workingTimeHalfTo,
        public ?string $workingTimeDayOffFrom,
        public ?string $workingTimeDayOffTo,
        public ?string $sameDayDepartureCutoff,
        public ?string $sameDayDepartureCutoffHalf,
        public ?string $sameDayDepartureCutoffDayOff,
        public ?ShipmentParcelSize $maxParcelDimensions,
        public ?float $maxParcelWeight,
        public OfficeType $type,
        public ?int $nearbyOfficeId,
        public array $workingTimeSchedule,
        public bool $palletOffice,
        public bool $cardPaymentAllowed,
        public bool $cashPaymentAllowed,
        public ?string $validFrom,
        public ?string $validTo,
        public array $cargoTypesAllowed,
        public bool $pickUpAllowed,
        public bool $dropOffAllowed,
        public ?RoutingInformation $routingInformation,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $workingTimeSchedule = [];

        foreach ($data['workingTimeSchedule'] ?? [] as $rawSchedule) {
            if (is_array($rawSchedule)) {
                $workingTimeSchedule[] = OfficeWorkingTimeSchedule::fromArray($rawSchedule);
            }
        }

        $cargoTypesAllowed = [];

        foreach ($data['cargoTypesAllowed'] ?? [] as $rawCargoType) {
            if (is_string($rawCargoType) && $rawCargoType !== '') {
                $cargoTypesAllowed[] = CargoType::from($rawCargoType);
            }
        }

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            siteId: (int) ($data['siteId'] ?? 0),
            address: is_array($data['address'] ?? null) ? ShipmentAddress::fromArray($data['address']) : null,
            workingTimeFrom: isset($data['workingTimeFrom']) ? (string) $data['workingTimeFrom'] : null,
            workingTimeTo: isset($data['workingTimeTo']) ? (string) $data['workingTimeTo'] : null,
            workingTimeHalfFrom: isset($data['workingTimeHalfFrom']) ? (string) $data['workingTimeHalfFrom'] : null,
            workingTimeHalfTo: isset($data['workingTimeHalfTo']) ? (string) $data['workingTimeHalfTo'] : null,
            workingTimeDayOffFrom: isset($data['workingTimeDayOffFrom']) ? (string) $data['workingTimeDayOffFrom'] : null,
            workingTimeDayOffTo: isset($data['workingTimeDayOffTo']) ? (string) $data['workingTimeDayOffTo'] : null,
            sameDayDepartureCutoff: isset($data['sameDayDepartureCutoff']) ? (string) $data['sameDayDepartureCutoff'] : null,
            sameDayDepartureCutoffHalf: isset($data['sameDayDepartureCutoffHalf']) ? (string) $data['sameDayDepartureCutoffHalf'] : null,
            sameDayDepartureCutoffDayOff: isset($data['sameDayDepartureCutoffDayOff']) ? (string) $data['sameDayDepartureCutoffDayOff'] : null,
            maxParcelDimensions: is_array($data['maxParcelDimensions'] ?? null) ? ShipmentParcelSize::fromArray($data['maxParcelDimensions']) : null,
            maxParcelWeight: isset($data['maxParcelWeight']) ? (float) $data['maxParcelWeight'] : null,
            type: OfficeType::from((string) ($data['type'] ?? OfficeType::Office->value)),
            nearbyOfficeId: isset($data['nearbyOfficeId']) ? (int) $data['nearbyOfficeId'] : null,
            workingTimeSchedule: $workingTimeSchedule,
            palletOffice: (bool) ($data['palletOffice'] ?? false),
            cardPaymentAllowed: (bool) ($data['cardPaymentAllowed'] ?? false),
            cashPaymentAllowed: (bool) ($data['cashPaymentAllowed'] ?? false),
            validFrom: isset($data['validFrom']) ? (string) $data['validFrom'] : null,
            validTo: isset($data['validTo']) ? (string) $data['validTo'] : null,
            cargoTypesAllowed: $cargoTypesAllowed,
            pickUpAllowed: (bool) ($data['pickUpAllowed'] ?? false),
            dropOffAllowed: (bool) ($data['dropOffAllowed'] ?? false),
            routingInformation: is_array($data['routingInformation'] ?? null) ? RoutingInformation::fromArray($data['routingInformation']) : null,
        );
    }
}
