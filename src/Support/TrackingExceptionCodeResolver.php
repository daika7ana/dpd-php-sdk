<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use Daika7ana\Dpd\DTOs\Tracking\Resolved\ResolvedTrackingExceptionCode;

final class TrackingExceptionCodeResolver
{
    /**
     * @var array<int, array{description: string, operationCodes: list<int>}>
     */
    private const MAP = [
        11 => ['description' => 'Wrong address', 'operationCodes' => [44, 111, 136, 164]],
        12 => ['description' => 'Refused by recipient - damaged', 'operationCodes' => [44, 111, 136]],
        14 => ['description' => 'Refused by recipient - not ordered', 'operationCodes' => [44, 111, 136]],
        15 => ['description' => 'Refused by recipient', 'operationCodes' => [44, 111, 136]],
        16 => ['description' => 'Refused by recipient - contents checked, found to be damaged', 'operationCodes' => [44, 111, 136]],
        19 => ['description' => 'Recipient not present - informed first time', 'operationCodes' => [44]],
        20 => ['description' => 'Self-collecting recipient', 'operationCodes' => [44, 136]],
        25 => ['description' => 'Closed (e.g. department store)', 'operationCodes' => [44]],
        29 => ['description' => 'Shipment incomplete', 'operationCodes' => [44, 111, 136]],
        37 => ['description' => 'Recipient not present - third time', 'operationCodes' => [44, 111]],
        42 => ['description' => 'Recipient not present, informed 2nd time', 'operationCodes' => [44, 111]],
        46 => ['description' => 'Amount not paid', 'operationCodes' => [44, 111, 136]],
        49 => ['description' => 'Customer\'s return', 'operationCodes' => [44, 111, 136]],
        66 => ['description' => 'ID check failed', 'operationCodes' => [44, 111, 136]],
        67 => ['description' => 'Signature refused', 'operationCodes' => [44, 111, 136]],
        80 => ['description' => 'Appointment scheduled', 'operationCodes' => [44, 136, 164]],
        84 => ['description' => 'Delivery note / order reference missing', 'operationCodes' => [44, 111, 136]],
        1002 => ['description' => 'Incomplete address', 'operationCodes' => [44, 136]],
        1004 => ['description' => 'In office - not claimed', 'operationCodes' => [111]],
        1005 => ['description' => 'Redirected', 'operationCodes' => [44]],
        1007 => ['description' => 'New date scheduled', 'operationCodes' => [44, 136, 164]],
        1018 => ['description' => 'Sender drop-off in office', 'operationCodes' => [164]],
        1019 => ['description' => 'Sender not present', 'operationCodes' => [164]],
        1020 => ['description' => 'Consignee not reachable - routed to office', 'operationCodes' => [44]],
        1021 => ['description' => 'Routed to office due to overload', 'operationCodes' => [136]],
        1022 => ['description' => 'Routed to office due to exceeded slot size', 'operationCodes' => [136]],
        1030 => ['description' => 'Wrong phone number', 'operationCodes' => [136]],
        9014 => ['description' => 'Closed national roads', 'operationCodes' => [181]],
        9015 => ['description' => 'Delay at border crossing', 'operationCodes' => [181]],
        9016 => ['description' => 'Severe weather conditions', 'operationCodes' => [181]],
        9017 => ['description' => 'Traffic accident', 'operationCodes' => [181]],
    ];

    public static function resolve(int $code): ResolvedTrackingExceptionCode
    {
        $resolved = self::MAP[$code] ?? [
            'description' => 'Unknown tracking exception code',
            'operationCodes' => [],
        ];

        return new ResolvedTrackingExceptionCode(
            code: $code,
            description: $resolved['description'],
            operationCodes: $resolved['operationCodes'],
        );
    }
}
