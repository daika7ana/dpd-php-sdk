<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Support\ApiResponseMapper;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ApiResponseMapperTest extends TestCase
{
    public function testExtractBinaryReturnsPdfContent(): void
    {
        $document = ApiResponseMapper::extractBinary(
            new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-test'),
            ['application/pdf', 'text/plain'],
        );

        $this->assertSame('application/pdf', $document->mimeType);
        $this->assertSame('%PDF-test', $document->contents);
    }

    public function testExtractCsvReturnsCsvContent(): void
    {
        $document = ApiResponseMapper::extractCsv(
            new Response(200, ['Content-Type' => 'text/csv; charset=utf-8'], "id,name\n1,RO\n"),
        );

        $this->assertSame('text/csv', $document->mimeType);
        $this->assertStringContainsString('id,name', $document->contents);
    }

    public function testExtractBinaryMapsJsonCredentialError(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid password.');

        ApiResponseMapper::extractBinary(
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'error' => [
                    'code' => 1,
                    'message' => 'Invalid password.',
                    'id' => 'err-auth',
                ],
            ], JSON_THROW_ON_ERROR)),
            ['application/pdf'],
        );
    }
}
