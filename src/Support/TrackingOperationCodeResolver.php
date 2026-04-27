<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use Daika7ana\Dpd\DTOs\Tracking\Resolved\ResolvedTrackingOperationCode;
use Daika7ana\Dpd\Enums\Tracking\TrackingOperationCode;

final class TrackingOperationCodeResolver
{
    public static function resolve(int $code): ResolvedTrackingOperationCode
    {
        $knownCode = TrackingOperationCode::tryFrom($code);

        return new ResolvedTrackingOperationCode(
            code: $code,
            description: $knownCode?->description() ?? 'Unknown tracking operation code',
            knownCode: $knownCode,
        );
    }
}
