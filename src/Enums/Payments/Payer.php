<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Payments;

enum Payer: string
{
    case Sender = 'SENDER';
    case Recipient = 'RECIPIENT';
    case ThirdParty = 'THIRD_PARTY';
}
