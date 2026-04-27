<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Locations;

enum NomenclatureSupport: int
{
    case None = 0;
    case Full = 1;
    case Partial = 2;
}
