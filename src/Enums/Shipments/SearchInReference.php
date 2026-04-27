<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Shipments;

enum SearchInReference: int
{
    case Ref1OrRef2 = 0;
    case Ref1 = 1;
    case Ref2 = 2;
}
