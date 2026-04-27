<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\ShipmentAddressPayload;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Validation\Requests\ValidateAddressRequest;
use Daika7ana\Dpd\DTOs\Validation\Requests\ValidatePhoneRequest;
use Daika7ana\Dpd\DTOs\Validation\Requests\ValidatePostCodeRequest;
use Daika7ana\Dpd\DTOs\Validation\Requests\ValidateShipmentRequest;
use Daika7ana\Dpd\DTOs\Validation\Responses\ValidationResult;
use Daika7ana\Dpd\Http\ApiRequest;

final readonly class ValidationResource
{
    public function __construct(
        private Client $client,
    ) {}

    public function address(ValidateAddressRequest|ShipmentAddressPayload $request): ValidationResult
    {
        $resolvedRequest = $request instanceof ValidateAddressRequest
            ? $request
            : new ValidateAddressRequest($request);

        return ValidationResult::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/validation/address', $resolvedRequest->toArray())),
        );
    }

    public function postCode(ValidatePostCodeRequest $request): ValidationResult
    {
        return ValidationResult::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/validation/postcode', $request->toArray())),
        );
    }

    public function phone(ValidatePhoneRequest|string $request, ?string $ext = null): ValidationResult
    {
        $resolvedRequest = $request instanceof ValidatePhoneRequest
            ? $request
            : new ValidatePhoneRequest($request, $ext);

        return ValidationResult::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/validation/phone', $resolvedRequest->toArray())),
        );
    }

    public function shipment(ValidateShipmentRequest|CreateShipmentRequest $request): ValidationResult
    {
        $resolvedRequest = $request instanceof ValidateShipmentRequest
            ? $request
            : new ValidateShipmentRequest($request);

        return ValidationResult::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/validation/shipment', $resolvedRequest->toArray())),
        );
    }
}
