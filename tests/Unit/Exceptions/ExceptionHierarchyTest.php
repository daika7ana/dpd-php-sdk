<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Exceptions;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\DpdException;
use Daika7ana\Dpd\Exceptions\TransportException;
use Daika7ana\Dpd\Exceptions\UnexpectedStatusException;
use Daika7ana\Dpd\Exceptions\ValidationException;
use Exception;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

final class ExceptionHierarchyTest extends TestCase
{
    public function testDpdExceptionExtendsException(): void
    {
        $exception = new DpdException('test');

        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertNotInstanceOf(RuntimeException::class, $exception);
    }

    public function testValidationExceptionExtendsDpdException(): void
    {
        $exception = new ValidationException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testAuthenticationExceptionExtendsDpdException(): void
    {
        $exception = new AuthenticationException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testUnexpectedStatusExceptionExtendsDpdException(): void
    {
        $exception = new UnexpectedStatusException(message: 'test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testTransportExceptionExtendsDpdException(): void
    {
        $exception = new TransportException('test');

        $this->assertInstanceOf(DpdException::class, $exception);
    }

    public function testTransportExceptionImplementsPsrClientException(): void
    {
        $exception = new TransportException('test');

        $this->assertInstanceOf(ClientExceptionInterface::class, $exception);
    }

    public function testHttpExceptionsExposeResponseRetryAfterAndError(): void
    {
        $error = DpdError::fromArray(['code' => 100, 'message' => 'Test error', 'id' => 'err-100']);

        foreach ([AuthenticationException::class, ValidationException::class, UnexpectedStatusException::class] as $class) {
            $exception = new $class(message: 'Test error', code: 100, error: $error, retryAfter: 30);

            $this->assertSame($error, $exception->error);
            $this->assertSame(30, $exception->retryAfter);
            $this->assertNull($exception->response);
        }
    }

    public function testUnexpectedStatusExceptionDerivesMessageFromResponseStatus(): void
    {
        $exception = new UnexpectedStatusException(response: new Response(503));

        $this->assertSame('Unexpected HTTP status 503', $exception->getMessage());
        $this->assertSame(503, $exception->response?->getStatusCode());
    }

    public function testUnexpectedStatusExceptionWithoutResponseUsesGenericMessage(): void
    {
        $this->assertSame('Unexpected HTTP status', (new UnexpectedStatusException(message: ''))->getMessage());
    }

    public function testUnexpectedStatusExceptionStoresResponseDetails(): void
    {
        $details = ['statusCode' => 400, 'body' => '{}'];

        $exception = new UnexpectedStatusException(message: 'Test error', responseDetails: $details);

        $this->assertSame($details, $exception->responseDetails);
    }
}
