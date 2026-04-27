<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Printing;

enum AdditionalWaybillSenderCopy: string
{
    case None = 'NONE';
    case OnSamePage = 'ON_SAME_PAGE';
    case OnSinglePage = 'ON_SINGLE_PAGE';
}
