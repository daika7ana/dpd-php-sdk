# Errors

The SDK maps transport failures and DPD API failures into a small exception hierarchy so callers do not need to inspect raw payload shapes everywhere.

## Exception Types

- `DpdException`: plain marker base extending `Exception` for SDK-specific failures
- `TransportException`: network or HTTP client failure before a valid DPD payload is mapped; implements `Psr\Http\Client\ClientExceptionInterface`
- `AuthenticationException`: HTTP `401` response
- `ValidationException`: HTTP `400` or `422` response
- `UnexpectedStatusException`: non-2xx HTTP status or a response shape/content type that does not match the expected decoder

`AuthenticationException`, `ValidationException` and `UnexpectedStatusException` expose the raw PSR-7 response as `$exception->response`, the numeric `Retry-After` header in seconds as `$exception->retryAfter` (`null` when absent or non-numeric; HTTP dates are not parsed), and the structured `DpdError` as `$exception->error`. `UnexpectedStatusException` additionally exposes metadata-only `$exception->responseDetails`; it never contains the raw body or payload.

`$exception->getCode()` is the HTTP status when a response is present, otherwise `0`. Carrier error codes are never placed on `getCode()`; read them from `$exception->error?->code`. The `context` and `component` fields of `DpdError` are carrier-defined and may be absent or differ per endpoint.

## Status Mapping

| HTTP status | Exception |
| --- | --- |
| `401` | `AuthenticationException` |
| `400`, `422` | `ValidationException` |
| `403` | `UnexpectedStatusException` (its `$error->isAuthenticationRelated()` is true) |
| any other non-2xx, or an unparseable/unexpected response shape | `UnexpectedStatusException` |

Only the HTTP status decides the exception class; payload content never changes it. A `403` is an unexpected status, but `DpdError::isAuthenticationRelated()` returns true so callers can still detect authorization failures from the error object. `$error->isAuthenticationRelated()` and `$error->isValidationRelated()` are advisory hints for consumers, not class selectors: a `500` with an auth-flavored or validation-coded payload is still an `UnexpectedStatusException` with the error attached.

## Typical Handling

```php
use Daika7ana\Dpd\Exceptions\DpdException;
use Daika7ana\Dpd\Exceptions\UnexpectedStatusException;

try {
	$shipment = $client->shipments()->create($shipmentRequest);
} catch (UnexpectedStatusException $exception) {
	$error = $exception->error;

	error_log($error?->message ?? $exception->getMessage());
} catch (DpdException $exception) {
	error_log($exception->getMessage());
}
```

Catch `DpdException` at integration boundaries and narrow to specific subclasses only when your application has distinct retry or recovery behavior.
