<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Http;

final readonly class ApiRequest
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, string|int> $pathParameters
     */
    public function __construct(
        public string $method,
        public string $path,
        public array $payload = [],
        public array $pathParameters = [],
        public string $accept = 'application/json',
    ) {}

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string|int> $pathParameters
     */
    public static function json(string $method, string $path, array $payload = [], array $pathParameters = []): self
    {
        return new self($method, $path, $payload, $pathParameters, 'application/json');
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string|int> $pathParameters
     */
    public static function binary(string $method, string $path, array $payload = [], array $pathParameters = []): self
    {
        return new self($method, $path, $payload, $pathParameters, 'application/pdf, text/plain, application/json');
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string|int> $pathParameters
     */
    public static function csv(string $method, string $path, array $payload = [], array $pathParameters = []): self
    {
        return new self($method, $path, $payload, $pathParameters, 'text/csv, application/csv, application/json');
    }
}
