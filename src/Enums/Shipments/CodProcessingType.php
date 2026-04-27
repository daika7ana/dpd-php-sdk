<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Shipments;

enum CodProcessingType: string
{
    case Cash = 'CASH';
    case PostalMoneyTransfer = 'POSTAL_MONEY_TRANSFER';
}
