<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Cache;

final class NullReferenceDataCache implements ReferenceDataCacheInterface
{
    public function get(string $key): mixed
    {
        return null;
    }

    public function has(string $key): bool
    {
        return false;
    }

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void {}

    public function forget(string $key): void {}

    public function clear(): void {}

    public function remember(string $key, callable $resolver, ?int $ttlSeconds = null): mixed
    {
        return $resolver();
    }
}
