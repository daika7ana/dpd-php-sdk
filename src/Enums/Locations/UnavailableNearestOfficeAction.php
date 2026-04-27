<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Locations;

enum UnavailableNearestOfficeAction: string
{
    case DeliveryToAddress = 'DELIVERY_TO_ADDRESS';
    case CancelWithError = 'CANCEL_WITH_ERROR';
}
