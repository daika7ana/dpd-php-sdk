<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Shipments;

enum SecondaryShipmentType: string
{
    case ReturnShipment = 'RETURN_SHIPMENT';
    case StoragePayment = 'STORAGE_PAYMENT';
    case Redirect = 'REDIRECT';
    case SendBack = 'SEND_BACK';
    case MoneyTransfer = 'MONEY_TRANSFER';
    case TransportDamaged = 'TRANSPORT_DAMAGED';
    case ReturnVoucher = 'RETURN_VOUCHER';
}
