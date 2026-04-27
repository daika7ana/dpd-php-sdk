<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Cache;

interface ReferenceDataCacheInterface
{
    public function get(string $key): mixed;

    public function has(string $key): bool;

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void;

    public function forget(string $key): void;

    public function clear(): void;

    public function remember(string $key, callable $resolver, ?int $ttlSeconds = null): mixed;
}
