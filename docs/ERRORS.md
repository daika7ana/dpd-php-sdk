# Errors

The SDK maps transport failures and DPD API failures into a small exception hierarchy so callers do not need to inspect raw payload shapes everywhere.

## Exception Types

- `DpdException`: base exception for SDK-specific failures
- `TransportException`: network or HTTP client failure before a valid DPD payload is mapped
- `AuthenticationException`: credential-related DPD failure
- `ApiException`: structured DPD API error response
- `ValidationException`: validation-focused API failure when DPD reports invalid request data
- `UnsupportedResponseException`: response shape or content type does not match the expected decoder

Structured API error fields are preserved on the attached `DpdError` object.

## Typical Handling

```php
use Daika7ana\Dpd\Exceptions\ApiException;
use Daika7ana\Dpd\Exceptions\DpdException;

try {
	$shipment = $client->shipments()->create($shipmentRequest);
} catch (ApiException $exception) {
	$error = $exception->getDpdError();

	report($error?->message ?? $exception->getMessage());
} catch (DpdException $exception) {
	report($exception->getMessage());
}
```

Catch `DpdException` at integration boundaries and narrow to specific subclasses only when your application has distinct retry or recovery behavior.
