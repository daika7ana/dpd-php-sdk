<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Support;

use Daika7ana\Dpd\Support\JsonHelper;
use JsonException;
use PHPUnit\Framework\TestCase;

final class JsonHelperTest extends TestCase
{
    public function testDecodeParsesValidJson(): void
    {
        $json = '{"id": 1, "name": "Test"}';

        $result = JsonHelper::decode($json);

        $this->assertSame(1, $result['id']);
        $this->assertSame('Test', $result['name']);
    }

    public function testDecodeThrowsOnMalformedJson(): void
    {
        $this->expectException(JsonException::class);

        JsonHelper::decode('{invalid json}');
    }

    public function testEncodeSerializesValue(): void
    {
        $result = JsonHelper::encode(['id' => 1, 'name' => 'Test']);

        $this->assertJson($result);
        $this->assertSame(1, json_decode($result, true)['id']);
    }

    public function testEncodeThrowsOnNonSerializableValue(): void
    {
        $this->expectException(JsonException::class);

        // NAN cannot be encoded to JSON
        JsonHelper::encode(NAN);
    }
}
