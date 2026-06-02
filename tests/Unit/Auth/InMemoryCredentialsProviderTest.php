<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Auth;

use Daika7ana\Dpd\Auth\Credentials;
use Daika7ana\Dpd\Auth\InMemoryCredentialsProvider;
use Daika7ana\Dpd\Config\ClientConfig;
use PHPUnit\Framework\TestCase;

final class InMemoryCredentialsProviderTest extends TestCase
{
    public function testGetCredentialsReturnsProvidedCredentials(): void
    {
        $credentials = new Credentials('user', 'pass', 'EN', 42);
        $provider = new InMemoryCredentialsProvider($credentials);

        $this->assertSame($credentials, $provider->getCredentials());
    }

    public function testSetCredentialsReplacesExistingCredentials(): void
    {
        $original = new Credentials('user', 'pass');
        $provider = new InMemoryCredentialsProvider($original);

        $updated = new Credentials('new-user', 'new-pass', 'BG', 99);
        $provider->setCredentials($updated);

        $this->assertSame($updated, $provider->getCredentials());
    }

    public function testFromConfigCreatesProviderFromConfig(): void
    {
        $config = new ClientConfig(
            username: 'demo',
            password: 'secret',
            language: 'EN',
            clientSystemId: 55,
        );

        $provider = InMemoryCredentialsProvider::fromConfig($config);

        $this->assertSame('demo', $provider->getCredentials()->username);
        $this->assertSame('secret', $provider->getCredentials()->password);
        $this->assertSame('EN', $provider->getCredentials()->language);
        $this->assertSame(55, $provider->getCredentials()->clientSystemId);
    }
}
