<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Exceptions;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Daika7ana\Dpd\Exceptions\ApiException;
use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\DpdException;
use Daika7ana\Dpd\Exceptions\TransportException;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExceptionHierarchyTest extends TestCase
{
    public function testDpdExceptionExtendsRuntimeException(): void
    {
        $exception = new DpdException('test');

        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testDpdExceptionStoresErrorAndResponseDetails(): void
    {
        $error = DpdError::fromArray(['code' => 100, 'message' => 'Test error', 'id' => 'err-100']);
        $details = ['statusCode' => 400, 'body' => '{}'];

        $exception = new DpdException(
            message: 'Test error',
            code: 100,
            error: $error,
            responseDetails: $details,
        );

        $this->assertSame($error, $exception->error);
        $this->assertSame($details, $exception->responseDetails);
    }

    public function testApiExceptionExtendsDpdException(): void
    {
        $exception = new ApiException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testValidationExceptionExtendsApiException(): void
    {
        $exception = new ValidationException('test');

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testAuthenticationExceptionExtendsDpdException(): void
    {
        $exception = new AuthenticationException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testTransportExceptionExtendsDpdException(): void
    {
        $exception = new TransportException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testUnsupportedResponseExceptionExtendsDpdException(): void
    {
        $exception = new UnsupportedResponseException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }
}
