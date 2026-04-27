<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Payments;

enum PayoutPaymentType: string
{
    case Cash = 'CASH';
    case Bank = 'BANK';
}
