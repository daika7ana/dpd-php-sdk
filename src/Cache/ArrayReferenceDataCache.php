<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Cache;

final class ArrayReferenceDataCache implements ReferenceDataCacheInterface
{
    private const MAX_ENTRIES = 50;

    /**
     * @var array<string, array{value: mixed, expiresAt: int|null}>
     */
    private array $items = [];

    public function get(string $key): mixed
    {
        $this->pruneIfExpired($key);

        return $this->items[$key]['value'] ?? null;
    }

    public function has(string $key): bool
    {
        $this->pruneIfExpired($key);

        return array_key_exists($key, $this->items);
    }

    public function put(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        unset($this->items[$key]);
        $this->pruneExpired();

        if (count($this->items) >= self::MAX_ENTRIES) {
            $oldestKey = array_key_first($this->items);

            if ($oldestKey !== null) {
                unset($this->items[$oldestKey]);
            }
        }

        $this->items[$key] = [
            'value' => $value,
            'expiresAt' => $ttlSeconds !== null ? time() + $ttlSeconds : null,
        ];
    }

    public function forget(string $key): void
    {
        unset($this->items[$key]);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function remember(string $key, callable $resolver, ?int $ttlSeconds = null): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $resolver();
        $this->put($key, $value, $ttlSeconds);

        return $value;
    }

    private function pruneExpired(): void
    {
        $now = time();

        foreach ($this->items as $key => $item) {
            if ($item['expiresAt'] !== null && $item['expiresAt'] < $now) {
                unset($this->items[$key]);
            }
        }
    }

    private function pruneIfExpired(string $key): void
    {
        if (!array_key_exists($key, $this->items)) {
            return;
        }

        $expiresAt = $this->items[$key]['expiresAt'];

        if ($expiresAt !== null && $expiresAt < time()) {
            unset($this->items[$key]);
        }
    }
}
