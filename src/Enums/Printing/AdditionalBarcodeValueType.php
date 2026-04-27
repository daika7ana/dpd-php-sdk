<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Printing;

enum AdditionalBarcodeValueType: string
{
    case Provided = 'PROVIDED';
    case ParcelId = 'PARCEL_ID';
}
