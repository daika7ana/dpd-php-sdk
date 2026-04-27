<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Services;

enum AdditionalServiceAllowance: string
{
    case Forbidden = 'FORBIDDEN';
    case Allowed = 'ALLOWED';
    case Required = 'REQUIRED';
}
