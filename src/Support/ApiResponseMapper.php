<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use Daika7ana\Dpd\DTOs\Common\Documents\BinaryContent;
use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Daika7ana\Dpd\Exceptions\ApiException;
use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Exceptions\ValidationException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

final class ApiResponseMapper
{
    /**
     * @return array<string, mixed>
     */
    public static function decodeJson(ResponseInterface $response): array
    {
        $contentType = self::normalizeContentType($response->getHeaderLine('Content-Type'));
        $body = (string) $response->getBody();
        $statusCode = $response->getStatusCode();

        if (($statusCode === 204 || $statusCode === 205) && trim($body) === '') {
            return [];
        }

        if ($contentType !== 'application/json') {
            if ($statusCode === 401 || $statusCode === 403) {
                throw new AuthenticationException(
                    message: sprintf('Authentication failed with HTTP status %d.', $statusCode),
                    responseDetails: self::responseDetails($response),
                );
            }

            throw new UnsupportedResponseException(
                message: sprintf('Expected JSON response, received "%s" (HTTP %d).', $contentType !== '' ? $contentType : 'unknown', $statusCode),
                responseDetails: self::responseDetails($response),
            );
        }

        $payload = self::decodePayload($response, $body);

        if (isset($payload['error']) && is_array($payload['error']) && $payload['error'] !== []) {
            self::throwMappedError($payload['error'], $response);
        }

        if ($statusCode === 401 || $statusCode === 403) {
            throw new AuthenticationException(
                message: sprintf('Authentication failed with HTTP status %d.', $statusCode),
                responseDetails: self::responseDetails($response),
            );
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new UnsupportedResponseException(
                message: sprintf('Unexpected HTTP status %d.', $statusCode),
                responseDetails: self::responseDetails($response),
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

            if (isset($payload['error']) && is_array($payload['error']) && $payload['error'] !== []) {
                self::throwMappedError($payload['error'], $response);
            }

            if ($response->getStatusCode() === 401 || $response->getStatusCode() === 403) {
                throw new AuthenticationException(
                    message: sprintf('Authentication failed with HTTP status %d.', $response->getStatusCode()),
                    responseDetails: self::responseDetails($response),
                );
            }

            throw new UnsupportedResponseException(
                message: 'Expected binary response, received JSON payload.',
                responseDetails: self::responseDetails($response),
            );
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            if ($response->getStatusCode() === 401 || $response->getStatusCode() === 403) {
                throw new AuthenticationException(
                    message: sprintf('Authentication failed with HTTP status %d.', $response->getStatusCode()),
                    responseDetails: self::responseDetails($response),
                );
            }

            throw new UnsupportedResponseException(
                message: sprintf('Unexpected HTTP status %d for binary response.', $response->getStatusCode()),
                responseDetails: self::responseDetails($response),
            );
        }

        if (!in_array($contentType, $acceptedContentTypes, true)) {
            throw new UnsupportedResponseException(
                message: sprintf('Unsupported binary content type "%s".', $contentType !== '' ? $contentType : 'unknown'),
                responseDetails: self::responseDetails($response),
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
        $error = DpdError::fromArray($errorPayload);
        $statusCode = $response->getStatusCode();
        $responseDetails = self::responseDetails($response, $error);

        if ($statusCode === 401 || $statusCode === 403 || $error->isAuthenticationRelated()) {
            throw new AuthenticationException(
                message: $error->message,
                code: $error->code,
                error: $error,
                responseDetails: $responseDetails,
            );
        }

        if ($error->isValidationRelated()) {
            throw new ValidationException(
                message: $error->message,
                code: $error->code,
                error: $error,
                responseDetails: $responseDetails,
            );
        }

        throw new ApiException(
            message: $error->message,
            code: $error->code,
            error: $error,
            responseDetails: $responseDetails,
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
            throw new UnsupportedResponseException(
                message: 'Response body is not valid JSON: ' . $exception->getMessage(),
                responseDetails: self::responseDetails($response),
                previous: $exception,
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
