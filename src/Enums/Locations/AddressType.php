<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Locations;

enum AddressType: int
{
    case Local = 1;
    case Foreign = 2;
}
