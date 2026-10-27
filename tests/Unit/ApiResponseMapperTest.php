<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Exceptions\ApiException;
use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
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

    public function testUnauthorizedJsonErrorMapsToAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Session expired');

        ApiResponseMapper::decodeJson(new Response(401, ['Content-Type' => 'application/json'], json_encode([
            'error' => ['code' => 7, 'message' => 'Session expired', 'id' => 'err-7'],
        ], JSON_THROW_ON_ERROR)));
    }

    public function testUnauthorizedNonJsonResponseMapsToAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Authentication failed with HTTP status 401');

        ApiResponseMapper::decodeJson(new Response(401, ['Content-Type' => 'text/html'], '<html>login</html>'));
    }

    public function testUnauthorizedJsonWithoutErrorMapsToAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Authentication failed with HTTP status 401');

        ApiResponseMapper::decodeJson(new Response(401, ['Content-Type' => 'application/json'], '{"message":"Unauthorized"}'));
    }

    public function testUnauthorizedJsonWithoutErrorMapsToAuthenticationExceptionForBinary(): void
    {
        $this->expectException(AuthenticationException::class);

        ApiResponseMapper::extractBinary(
            new Response(401, ['Content-Type' => 'application/json'], '{"message":"Unauthorized"}'),
            ['application/pdf'],
        );
    }

    public function testEmptySuccessBodyIsNotTreatedAsNoContent(): void
    {
        $this->expectException(UnsupportedResponseException::class);

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], ''));
    }

    public function testMalformedJsonThrowsUnsupportedResponseException(): void
    {
        $this->expectException(UnsupportedResponseException::class);
        $this->expectExceptionMessage('Response body is not valid JSON');

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], '{bad'));
    }

    public function testScalarJsonBodyThrowsUnsupportedResponseException(): void
    {
        $this->expectException(UnsupportedResponseException::class);
        $this->expectExceptionMessage('not valid JSON');

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], 'null'));
    }

    public function testNoContentResponseDecodesToEmptyArray(): void
    {
        $this->assertSame([], ApiResponseMapper::decodeJson(new Response(204)));
    }

    public function testResponseDetailsDoNotKeepTheRawBody(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(400, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 100, 'message' => 'Invalid address: Str. Victoriei 12', 'id' => 'err-100'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected ApiException.');
        } catch (ApiException $exception) {
            $this->assertArrayNotHasKey('body', $exception->responseDetails);
            $this->assertArrayNotHasKey('payload', $exception->responseDetails);
            $this->assertSame(400, $exception->responseDetails['statusCode']);
        }
    }
}
