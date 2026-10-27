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
        public OfficeType $type,
        public array $workingTimeSchedule,
        public bool $palletOffice,
        public bool $cardPaymentAllowed,
        public bool $cashPaymentAllowed,
        public array $cargoTypesAllowed,
        public bool $pickUpAllowed,
        public bool $dropOffAllowed,
        public ?ShipmentAddress $address = null,
        public ?string $workingTimeFrom = null,
        public ?string $workingTimeTo = null,
        public ?string $workingTimeHalfFrom = null,
        public ?string $workingTimeHalfTo = null,
        public ?string $workingTimeDayOffFrom = null,
        public ?string $workingTimeDayOffTo = null,
        public ?string $sameDayDepartureCutoff = null,
        public ?string $sameDayDepartureCutoffHalf = null,
        public ?string $sameDayDepartureCutoffDayOff = null,
        public ?ShipmentParcelSize $maxParcelDimensions = null,
        public ?float $maxParcelWeight = null,
        public ?int $nearbyOfficeId = null,
        public ?string $validFrom = null,
        public ?string $validTo = null,
        public ?RoutingInformation $routingInformation = null,
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
                $cargoType = CargoType::tryFrom((string) $rawCargoType);
                if ($cargoType !== null) {
                    $cargoTypesAllowed[] = $cargoType;
                }
            }
        }

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            siteId: (int) ($data['siteId'] ?? 0),
            type: OfficeType::tryFrom((string) ($data['type'] ?? '')) ?? OfficeType::Office,
            workingTimeSchedule: $workingTimeSchedule,
            palletOffice: (bool) ($data['palletOffice'] ?? false),
            cardPaymentAllowed: (bool) ($data['cardPaymentAllowed'] ?? false),
            cashPaymentAllowed: (bool) ($data['cashPaymentAllowed'] ?? false),
            cargoTypesAllowed: $cargoTypesAllowed,
            pickUpAllowed: (bool) ($data['pickUpAllowed'] ?? false),
            dropOffAllowed: (bool) ($data['dropOffAllowed'] ?? false),
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
            nearbyOfficeId: isset($data['nearbyOfficeId']) ? (int) $data['nearbyOfficeId'] : null,
            validFrom: isset($data['validFrom']) ? (string) $data['validFrom'] : null,
            validTo: isset($data['validTo']) ? (string) $data['validTo'] : null,
            routingInformation: is_array($data['routingInformation'] ?? null) ? RoutingInformation::fromArray($data['routingInformation']) : null,
        );
    }
}
