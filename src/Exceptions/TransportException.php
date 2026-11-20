<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;

class TransportException extends DpdException implements ClientExceptionInterface {}
