<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

use Daika7ana\Dpd\DTOs\Common\Documents\BinaryContent;
use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use Daika7ana\Dpd\Exceptions\ApiException;
use Daika7ana\Dpd\Exceptions\AuthenticationException;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Exceptions\ValidationException;
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

        if ($contentType !== 'application/json') {
            throw new UnsupportedResponseException(
                message: sprintf('Expected JSON response, received "%s".', $contentType !== '' ? $contentType : 'unknown'),
                responseDetails: self::responseDetails($response, $body),
            );
        }

        $payload = JsonHelper::decode($body);

        if (isset($payload['error']) && is_array($payload['error']) && $payload['error'] !== []) {
            self::throwMappedError($payload['error'], $response, $body);
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new UnsupportedResponseException(
                message: sprintf('Unexpected HTTP status %d.', $response->getStatusCode()),
                responseDetails: self::responseDetails($response, $body, $payload),
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
            $payload = JsonHelper::decode($body);

            if (isset($payload['error']) && is_array($payload['error']) && $payload['error'] !== []) {
                self::throwMappedError($payload['error'], $response, $body);
            }

            throw new UnsupportedResponseException(
                message: 'Expected binary response, received JSON payload.',
                responseDetails: self::responseDetails($response, $body, $payload),
            );
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new UnsupportedResponseException(
                message: sprintf('Unexpected HTTP status %d for binary response.', $response->getStatusCode()),
                responseDetails: self::responseDetails($response, $body),
            );
        }

        if (!in_array($contentType, $acceptedContentTypes, true)) {
            throw new UnsupportedResponseException(
                message: sprintf('Unsupported binary content type "%s".', $contentType !== '' ? $contentType : 'unknown'),
                responseDetails: self::responseDetails($response, $body),
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
    private static function throwMappedError(array $errorPayload, ResponseInterface $response, string $body): never
    {
        $error = DpdError::fromArray($errorPayload);
        $responseDetails = self::responseDetails($response, $body, ['error' => $errorPayload]);

        if ($error->isAuthenticationRelated()) {
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
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private static function responseDetails(ResponseInterface $response, string $body, array $payload = []): array
    {
        return [
            'statusCode' => $response->getStatusCode(),
            'contentType' => self::normalizeContentType($response->getHeaderLine('Content-Type')),
            'body' => $body,
            'payload' => $payload,
        ];
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
