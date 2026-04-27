<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Parties;

use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Enums\Locations\UnavailableNearestOfficeAction;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class AutoSelectNearestOfficePolicy
{
    public function __construct(
        public ?UnavailableNearestOfficeAction $unavailableNearestOfficeAction = null,
        public ?OfficeType $officeType = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'unavailableNearestOfficeAction' => $this->unavailableNearestOfficeAction?->value,
            'officeType' => $this->officeType?->value,
        ]);
    }
}
