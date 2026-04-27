<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use JsonException;

final class JsonHelper
{
    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    public static function decode(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public static function encode(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
