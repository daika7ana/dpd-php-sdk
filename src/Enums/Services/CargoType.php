<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Services;

enum CargoType: string
{
    case Parcel = 'PARCEL';
    case Pallet = 'PALLET';
    case Tyre = 'TYRE';
}
