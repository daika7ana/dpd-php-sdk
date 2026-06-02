<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Auth;

use Daika7ana\Dpd\Auth\Credentials;
use Daika7ana\Dpd\Config\ClientConfig;
use PHPUnit\Framework\TestCase;

final class CredentialsTest extends TestCase
{
    public function testFromConfigMapsAllFields(): void
    {
        $config = new ClientConfig(
            username: 'user',
            password: 'pass',
            language: 'EN',
            clientSystemId: 42,
        );

        $credentials = Credentials::fromConfig($config);

        $this->assertSame('user', $credentials->username);
        $this->assertSame('pass', $credentials->password);
        $this->assertSame('EN', $credentials->language);
        $this->assertSame(42, $credentials->clientSystemId);
    }

    public function testToRequestPayloadIncludesClientSystemIdWhenSet(): void
    {
        $credentials = new Credentials(
            username: 'user',
            password: 'pass',
            language: 'EN',
            clientSystemId: 42,
        );

        $payload = $credentials->toRequestPayload();

        $this->assertSame('user', $payload['userName']);
        $this->assertSame('pass', $payload['password']);
        $this->assertSame('EN', $payload['language']);
        $this->assertSame(42, $payload['clientSystemId']);
    }

    public function testToRequestPayloadOmitsClientSystemIdWhenNull(): void
    {
        $credentials = new Credentials(
            username: 'user',
            password: 'pass',
            language: 'EN',
            clientSystemId: null,
        );

        $payload = $credentials->toRequestPayload();

        $this->assertArrayNotHasKey('clientSystemId', $payload);
    }

    public function testConstructorDefaultsToRomanianLanguage(): void
    {
        $credentials = new Credentials(
            username: 'user',
            password: 'pass',
        );

        $this->assertSame('RO', $credentials->language);
        $this->assertNull($credentials->clientSystemId);
    }
}
