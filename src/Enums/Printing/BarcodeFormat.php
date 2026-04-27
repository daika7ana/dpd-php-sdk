<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Printing;

enum BarcodeFormat: string
{
    case Code128 = 'CODE128';
    case Ean13 = 'EAN13';
    case Ean8 = 'EAN8';
    case Upca = 'UPCA';
    case Upce = 'UPCE';
}
