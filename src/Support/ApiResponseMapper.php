<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use Daika7ana\Dpd\DTOs\Common\Documents\BinaryContent;
use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\UnexpectedStatusException;
use Daika7ana\Dpd\Exceptions\ValidationException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

final class ApiResponseMapper
{
    /**
     * @return array<string, mixed>
     */
    public static function decodeJson(ResponseInterface $response, bool $throwOnError = true): array
    {
        $contentType = self::normalizeContentType($response->getHeaderLine('Content-Type'));
        $body = (string) $response->getBody();
        $statusCode = $response->getStatusCode();

        if (($statusCode === 204 || $statusCode === 205) && trim($body) === '') {
            return [];
        }

        if ($contentType !== 'application/json') {
            if ($statusCode === 401) {
                throw new AuthenticationException(
                    message: sprintf('Authentication failed with HTTP status %d.', $statusCode),
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            if ($statusCode === 400 || $statusCode === 422) {
                throw new ValidationException(
                    message: sprintf('DPD API returned HTTP %d (%s).', $statusCode, $contentType !== '' ? $contentType : 'unknown content type'),
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            if ($statusCode < 200 || $statusCode >= 300) {
                throw new UnexpectedStatusException(
                    message: sprintf('DPD API returned HTTP %d (%s).', $statusCode, $contentType !== '' ? $contentType : 'unknown content type'),
                    responseDetails: self::responseDetails($response),
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            throw new UnexpectedStatusException(
                message: sprintf('Expected JSON response, received "%s" (HTTP %d).', $contentType !== '' ? $contentType : 'unknown', $statusCode),
                responseDetails: self::responseDetails($response),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        $payload = self::decodePayload($response, $body);

        if (isset($payload['error']) && is_array($payload['error']) && $payload['error'] !== []) {
            if ($throwOnError || $statusCode < 200 || $statusCode >= 300) {
                self::throwMappedError($payload['error'], $response);
            }
        }

        if ($statusCode === 401) {
            throw new AuthenticationException(
                message: sprintf('Authentication failed with HTTP status %d.', $statusCode),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        if ($statusCode === 400 || $statusCode === 422) {
            throw new ValidationException(
                message: sprintf('DPD API returned HTTP %d.', $statusCode),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new UnexpectedStatusException(
                message: sprintf('Unexpected HTTP status %d.', $statusCode),
                responseDetails: self::responseDetails($response),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        return $payload;
    }

    /**
     * @param list<string> $acceptedContentTypes
     */
    public static function extractBinary(ResponseInterface $response, array $acceptedContentTypes): BinaryContent
    {
        $contentType = self::normalizeContentType($response->getHeaderLine('Content-Type'));
        $body = (string) $response->getBody();

        if ($contentType === 'application/json') {
            $payload = self::decodePayload($response, $body);
            $error = isset($payload['error']) && is_array($payload['error']) && $payload['error'] !== []
                ? DpdError::fromArray($payload['error'], $response->getStatusCode())
                : null;

            if ($error !== null) {
                self::throwMappedError($payload['error'], $response);
            }

            if ($response->getStatusCode() === 401) {
                throw new AuthenticationException(
                    message: sprintf('Authentication failed with HTTP status %d.', $response->getStatusCode()),
                    code: $response->getStatusCode(),
                    error: $error,
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            if ($response->getStatusCode() === 400 || $response->getStatusCode() === 422) {
                throw new ValidationException(
                    message: sprintf('DPD API returned HTTP %d for binary response.', $response->getStatusCode()),
                    code: $response->getStatusCode(),
                    error: $error,
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            throw new UnexpectedStatusException(
                message: 'Expected binary response, received JSON payload.',
                code: $response->getStatusCode(),
                error: $error,
                responseDetails: self::responseDetails($response, $error),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            if ($response->getStatusCode() === 401) {
                throw new AuthenticationException(
                    message: sprintf('Authentication failed with HTTP status %d.', $response->getStatusCode()),
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            if ($response->getStatusCode() === 400 || $response->getStatusCode() === 422) {
                throw new ValidationException(
                    message: sprintf('DPD API returned HTTP %d for binary response.', $response->getStatusCode()),
                    response: $response,
                    retryAfter: self::retryAfter($response),
                );
            }

            throw new UnexpectedStatusException(
                message: sprintf('DPD API returned HTTP %d for binary response.', $response->getStatusCode()),
                responseDetails: self::responseDetails($response),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        if (!in_array($contentType, $acceptedContentTypes, true)) {
            throw new UnexpectedStatusException(
                message: sprintf('Unsupported binary content type "%s".', $contentType !== '' ? $contentType : 'unknown'),
                responseDetails: self::responseDetails($response),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        return new BinaryContent($body, $contentType);
    }

    public static function extractCsv(ResponseInterface $response): BinaryContent
    {
        return self::extractBinary($response, [
            'text/csv',
            'application/csv',
            'application/octet-stream',
            'text/plain',
        ]);
    }

    /**
     * @param array<string, mixed> $errorPayload
     */
    private static function throwMappedError(array $errorPayload, ResponseInterface $response): never
    {
        $statusCode = $response->getStatusCode();
        $error = DpdError::fromArray($errorPayload, $statusCode);
        $responseDetails = self::responseDetails($response, $error);

        if ($statusCode === 401) {
            throw new AuthenticationException(
                message: $error->message,
                code: $statusCode,
                error: $error,
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        if ($statusCode === 400 || $statusCode === 422) {
            throw new ValidationException(
                message: $error->message,
                code: $statusCode,
                error: $error,
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }

        throw new UnexpectedStatusException(
            message: $error->message,
            code: $statusCode,
            error: $error,
            responseDetails: $responseDetails,
            response: $response,
            retryAfter: self::retryAfter($response),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function decodePayload(ResponseInterface $response, string $body): array
    {
        try {
            return JsonHelper::decode($body);
        } catch (JsonException $exception) {
            throw new UnexpectedStatusException(
                message: 'Response body is not valid JSON: ' . $exception->getMessage(),
                responseDetails: self::responseDetails($response),
                response: $response,
                retryAfter: self::retryAfter($response),
            );
        }
    }

    /**
     * Keeps only non-sensitive metadata: response bodies often echo customer data.
     *
     * @return array<string, mixed>
     */
    private static function responseDetails(ResponseInterface $response, ?DpdError $error = null): array
    {
        return array_filter([
            'statusCode' => $response->getStatusCode(),
            'contentType' => self::normalizeContentType($response->getHeaderLine('Content-Type')),
            'errorCode' => $error?->code,
            'errorMessage' => $error?->message,
        ], static fn(mixed $value): bool => $value !== null);
    }

    public static function retryAfter(ResponseInterface $response): ?int
    {
        $value = trim($response->getHeaderLine('Retry-After'));

        return ctype_digit($value) ? (int) $value : null;
    }

    private static function normalizeContentType(string $contentType): string
    {
        $normalized = trim(strtolower($contentType));

        if ($normalized === '') {
            return '';
        }

        $segments = explode(';', $normalized);

        return trim($segments[0]);
    }
}
