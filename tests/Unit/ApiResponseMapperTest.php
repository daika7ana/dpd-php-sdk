<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit;

use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\UnexpectedStatusException;
use Daika7ana\Dpd\Exceptions\ValidationException;
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

    public function testExtractBinaryJsonErrorPayloadOnSuccessMapsToUnexpectedStatusException(): void
    {
        try {
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
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertSame(200, $exception->getCode());
            $this->assertTrue($exception->error?->isAuthenticationRelated() ?? false);
        }
    }

    public function testServerErrorWithAuthFlavoredPayloadStaysUnexpectedStatusException(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 1, 'message' => 'Invalid password.', 'id' => 'err-auth'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertSame(500, $exception->getCode());
            $this->assertTrue($exception->error?->isAuthenticationRelated() ?? false);
        }
    }

    public function testServerErrorWithValidationCodedPayloadStaysUnexpectedStatusException(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 120, 'message' => 'Invalid street.', 'id' => 'err-120'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertSame(500, $exception->getCode());
            $this->assertTrue($exception->error?->isValidationRelated() ?? false);
        }
    }

    public function testSuccessWithValidationCodedPayloadStaysUnexpectedStatusException(): void
    {
        $this->expectException(UnexpectedStatusException::class);

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'error' => ['code' => 120, 'message' => 'Invalid street.', 'id' => 'err-120'],
        ], JSON_THROW_ON_ERROR)));
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
        $this->expectException(UnexpectedStatusException::class);

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], ''));
    }

    public function testMalformedJsonThrowsUnexpectedStatusException(): void
    {
        $this->expectException(UnexpectedStatusException::class);
        $this->expectExceptionMessage('Response body is not valid JSON');

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], '{bad'));
    }

    public function testScalarJsonBodyThrowsUnexpectedStatusException(): void
    {
        $this->expectException(UnexpectedStatusException::class);
        $this->expectExceptionMessage('not valid JSON');

        ApiResponseMapper::decodeJson(new Response(200, ['Content-Type' => 'application/json'], 'null'));
    }

    public function testNoContentResponseDecodesToEmptyArray(): void
    {
        $this->assertSame([], ApiResponseMapper::decodeJson(new Response(204)));
    }

    public function testNonJsonBadRequestMapsToValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('HTTP 400');

        ApiResponseMapper::decodeJson(new Response(400, ['Content-Type' => 'text/plain'], 'Cannot deserialize value'));
    }

    public function testBadRequestJsonErrorMapsToValidationExceptionWithHttpStatusAsCode(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(400, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 999, 'message' => 'Invalid address.', 'id' => 'err-999'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertSame(400, $exception->getCode());
            $this->assertSame(999, $exception->error?->code);
        }
    }

    public function testForbiddenResponseMapsToUnexpectedStatusWithAuthRelatedError(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(403, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 403, 'message' => 'Access denied', 'id' => 'err-403'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertSame(403, $exception->getCode());
            $this->assertSame(403, $exception->response?->getStatusCode());
            $this->assertTrue($exception->error?->isAuthenticationRelated() ?? false);
        }
    }

    public function testForbiddenNonJsonResponseMapsToUnexpectedStatusException(): void
    {
        $this->expectException(UnexpectedStatusException::class);

        ApiResponseMapper::decodeJson(new Response(403, ['Content-Type' => 'text/plain'], 'Forbidden'));
    }

    public function testUnprocessableEntityMapsToValidationException(): void
    {
        $this->expectException(ValidationException::class);

        ApiResponseMapper::decodeJson(new Response(422, ['Content-Type' => 'application/json'], '{}'));
    }

    public function testMappedCarrierErrorKeepsCarrierCodeOnErrorOnly(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(401, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 7, 'message' => 'Session expired', 'id' => 'err-7'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected AuthenticationException.');
        } catch (AuthenticationException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(7, $exception->error?->code);
        }
    }

    public function testDecodeJsonCanReturnErrorPayloadWithoutThrowing(): void
    {
        $payload = ApiResponseMapper::decodeJson(
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'valid' => false,
                'error' => ['code' => 120, 'message' => 'Invalid street.', 'id' => 'err-120'],
            ], JSON_THROW_ON_ERROR)),
            throwOnError: false,
        );

        $this->assertFalse($payload['valid']);
        $this->assertSame(120, $payload['error']['code']);
    }

    public function testHttpErrorExceptionExposesRawPsr7Response(): void
    {
        $response = new Response(500, ['Content-Type' => 'application/json'], json_encode([
            'error' => ['code' => 999, 'message' => 'Upstream failure.', 'id' => 'err-999'],
        ], JSON_THROW_ON_ERROR));

        try {
            ApiResponseMapper::decodeJson($response);
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertSame($response, $exception->response);
            $this->assertSame(500, $exception->response->getStatusCode());
            $this->assertSame(500, $exception->responseDetails['statusCode']);
        }
    }

    public function testResponseDetailsDoNotKeepTheRawBody(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'error' => ['code' => 999, 'message' => 'Invalid address: Str. Victoriei 12', 'id' => 'err-999'],
            ], JSON_THROW_ON_ERROR)));
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertArrayNotHasKey('body', $exception->responseDetails);
            $this->assertArrayNotHasKey('payload', $exception->responseDetails);
            $this->assertSame(500, $exception->responseDetails['statusCode']);
        }
    }

    public function testExceptionsExposeRetryAfterHeader(): void
    {
        try {
            ApiResponseMapper::decodeJson(new Response(429, ['Retry-After' => '30'], 'rate limited'));
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertSame(30, $exception->retryAfter);
        }

        try {
            ApiResponseMapper::decodeJson(new Response(429, [], 'rate limited'));
            $this->fail('Expected UnexpectedStatusException.');
        } catch (UnexpectedStatusException $exception) {
            $this->assertNull($exception->retryAfter);
        }
    }
}
