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
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new JsonException('JSON payload must be an object or an array.');
        }

        return $decoded;
    }

    /**
     * @throws JsonException
     */
    public static function encode(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
