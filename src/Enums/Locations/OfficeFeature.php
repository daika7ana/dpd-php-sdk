<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Locations;

enum OfficeFeature: string
{
    case CardPayment = 'CARD_PAYMENT';
    case CashPayment = 'CASH_PAYMENT';
    case DropOff = 'DROP_OFF';
    case PickUp = 'PICK_UP';
    case CargoTypeParcel = 'CARGO_TYPE_PARCEL';
    case CargoTypePallet = 'CARGO_TYPE_PALLET';
    case CargoTypeTyre = 'CARGO_TYPE_TYRE';
}
