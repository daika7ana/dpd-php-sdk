<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

final class PayloadFilter
{
    private function __construct() {}

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public static function removeNulls(array $payload): array
    {
        return array_filter($payload, static fn(mixed $value): bool => $value !== null && $value !== []);
    }
}
