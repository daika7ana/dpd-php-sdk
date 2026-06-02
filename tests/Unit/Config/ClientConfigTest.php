<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Config;

use Daika7ana\Dpd\Config\ClientConfig;
use PHPUnit\Framework\TestCase;

final class ClientConfigTest extends TestCase
{
    public function testConstructorNormalizesBaseUrlTrailingSlash(): void
    {
        $config = new ClientConfig(baseUrl: 'https://api.dpd.ro/v1/');

        $this->assertSame('https://api.dpd.ro/v1', $config->baseUrl);
    }

    public function testConstructorNormalizesLanguageToUppercase(): void
    {
        $config = new ClientConfig(language: 'en');

        $this->assertSame('EN', $config->language);
    }

    public function testWithBaseUrlReturnsNewInstance(): void
    {
        $original = new ClientConfig(username: 'user', password: 'pass');
        $modified = $original->withBaseUrl('https://custom.api.com/v1');

        $this->assertNotSame($original, $modified);
        $this->assertSame('https://custom.api.com/v1', $modified->baseUrl);
        $this->assertSame('user', $modified->username);
    }

    public function testWithCredentialsReturnsNewInstance(): void
    {
        $original = new ClientConfig(username: 'old', password: 'old-pass');
        $modified = $original->withCredentials('new', 'new-pass');

        $this->assertSame('new', $modified->username);
        $this->assertSame('new-pass', $modified->password);
        $this->assertSame('old', $original->username);
    }

    public function testWithLanguageReturnsNewInstance(): void
    {
        $original = new ClientConfig(language: 'RO');
        $modified = $original->withLanguage('EN');

        $this->assertSame('EN', $modified->language);
        $this->assertSame('RO', $original->language);
    }

    public function testWithClientSystemIdReturnsNewInstance(): void
    {
        $original = new ClientConfig(clientSystemId: null);
        $modified = $original->withClientSystemId(42);

        $this->assertSame(42, $modified->clientSystemId);
        $this->assertNull($original->clientSystemId);
    }

    public function testWithTimeoutsReturnsNewInstance(): void
    {
        $original = new ClientConfig();
        $modified = $original->withTimeouts(30.0, 5.0);

        $this->assertSame(30.0, $modified->timeout);
        $this->assertSame(5.0, $modified->connectTimeout);
        $this->assertNull($original->timeout);
    }

    public function testFromEnvironmentReadsEnvVars(): void
    {
        putenv('DPD_BASE_URL=https://test.api.com/v1');
        putenv('DPD_USERNAME=test-user');
        putenv('DPD_PASSWORD=test-pass');
        putenv('DPD_LANGUAGE=bg');
        putenv('DPD_CLIENT_SYSTEM_ID=77');
        putenv('DPD_TIMEOUT=25.5');
        putenv('DPD_CONNECT_TIMEOUT=3.5');

        $config = ClientConfig::fromEnvironment();

        $this->assertSame('https://test.api.com/v1', $config->baseUrl);
        $this->assertSame('test-user', $config->username);
        $this->assertSame('test-pass', $config->password);
        $this->assertSame('BG', $config->language);
        $this->assertSame(77, $config->clientSystemId);
        $this->assertSame(25.5, $config->timeout);
        $this->assertSame(3.5, $config->connectTimeout);

        putenv('DPD_BASE_URL');
        putenv('DPD_USERNAME');
        putenv('DPD_PASSWORD');
        putenv('DPD_LANGUAGE');
        putenv('DPD_CLIENT_SYSTEM_ID');
        putenv('DPD_TIMEOUT');
        putenv('DPD_CONNECT_TIMEOUT');
    }

    public function testFromEnvironmentDefaultsWhenEnvVarsEmpty(): void
    {
        putenv('DPD_BASE_URL');
        putenv('DPD_USERNAME');
        putenv('DPD_PASSWORD');
        putenv('DPD_LANGUAGE');
        putenv('DPD_CLIENT_SYSTEM_ID');
        putenv('DPD_TIMEOUT');
        putenv('DPD_CONNECT_TIMEOUT');

        $config = ClientConfig::fromEnvironment();

        $this->assertSame(ClientConfig::BASE_URL_PRODUCTION, $config->baseUrl);
        $this->assertSame('', $config->username);
        $this->assertSame('', $config->password);
        $this->assertSame('RO', $config->language);
        $this->assertNull($config->clientSystemId);
        $this->assertNull($config->timeout);
        $this->assertNull($config->connectTimeout);
    }
}
