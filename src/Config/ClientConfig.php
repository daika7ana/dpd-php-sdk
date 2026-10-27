<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Config;

final class ClientConfig
{
    public const BASE_URL_PRODUCTION = 'https://api.dpd.ro/v1';

    public const DEFAULT_TIMEOUT = 30.0;

    public const DEFAULT_CONNECT_TIMEOUT = 10.0;

    public readonly string $baseUrl;

    public readonly string $username;

    public readonly string $password;

    public readonly string $language;

    public readonly ?int $clientSystemId;

    public readonly float $timeout;

    public readonly float $connectTimeout;

    public function __construct(
        string $baseUrl = self::BASE_URL_PRODUCTION,
        string $username = '',
        string $password = '',
        string $language = 'RO',
        ?int $clientSystemId = null,
        ?float $timeout = self::DEFAULT_TIMEOUT,
        ?float $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT,
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->password = $password;
        $this->language = strtoupper($language);
        $this->clientSystemId = $clientSystemId;
        $this->timeout = $timeout ?? self::DEFAULT_TIMEOUT;
        $this->connectTimeout = $connectTimeout ?? self::DEFAULT_CONNECT_TIMEOUT;
    }

    public function withBaseUrl(string $baseUrl): self
    {
        return new self(
            baseUrl: $baseUrl,
            username: $this->username,
            password: $this->password,
            language: $this->language,
            clientSystemId: $this->clientSystemId,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
        );
    }

    public function withCredentials(string $username, string $password): self
    {
        return new self(
            baseUrl: $this->baseUrl,
            username: $username,
            password: $password,
            language: $this->language,
            clientSystemId: $this->clientSystemId,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
        );
    }

    public function withLanguage(string $language): self
    {
        return new self(
            baseUrl: $this->baseUrl,
            username: $this->username,
            password: $this->password,
            language: $language,
            clientSystemId: $this->clientSystemId,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
        );
    }

    public function withClientSystemId(?int $clientSystemId): self
    {
        return new self(
            baseUrl: $this->baseUrl,
            username: $this->username,
            password: $this->password,
            language: $this->language,
            clientSystemId: $clientSystemId,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
        );
    }

    public function withTimeouts(?float $timeout = null, ?float $connectTimeout = null): self
    {
        return new self(
            baseUrl: $this->baseUrl,
            username: $this->username,
            password: $this->password,
            language: $this->language,
            clientSystemId: $this->clientSystemId,
            timeout: $timeout ?? $this->timeout,
            connectTimeout: $connectTimeout ?? $this->connectTimeout,
        );
    }

    public static function fromEnvironment(): self
    {
        return new self(
            baseUrl: (string) (getenv('DPD_BASE_URL') ?: self::BASE_URL_PRODUCTION),
            username: (string) (getenv('DPD_USERNAME') ?: ''),
            password: (string) (getenv('DPD_PASSWORD') ?: ''),
            language: (string) (getenv('DPD_LANGUAGE') ?: 'RO'),
            clientSystemId: self::parseNullableInt(getenv('DPD_CLIENT_SYSTEM_ID') ?: null),
            timeout: self::parseNullableFloat(getenv('DPD_TIMEOUT') ?: null),
            connectTimeout: self::parseNullableFloat(getenv('DPD_CONNECT_TIMEOUT') ?: null),
        );
    }

    private static function parseNullableInt(false|string|null $value): ?int
    {
        if ($value === null || $value === false || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private static function parseNullableFloat(false|string|null $value): ?float
    {
        if ($value === null || $value === false || $value === '' || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
