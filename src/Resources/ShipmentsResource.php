<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Shipments\Requests\AddParcelRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\BarcodeInformationRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CancelShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\FinalizePendingShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\FindParcelsByReferenceRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\HandoverToCourierRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\HandoverToMidwayCarrierRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\SecondaryShipmentsRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\ShipmentInformationRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\UpdateShipmentPropertiesRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\UpdateShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Responses\AddParcelResponse;
use Daika7ana\Dpd\DTOs\Shipments\Responses\BarcodeInformationResponse;
use Daika7ana\Dpd\DTOs\Shipments\Responses\CreateShipmentResponse;
use Daika7ana\Dpd\DTOs\Shipments\Responses\FindParcelsByReferenceResponse;
use Daika7ana\Dpd\DTOs\Shipments\Responses\SecondaryShipmentsResponse;
use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentInformationResponse;
use Daika7ana\Dpd\Http\ApiRequest;

final readonly class ShipmentsResource
{
    public function __construct(
        private Client $client,
    ) {}

    public function create(CreateShipmentRequest $request): CreateShipmentResponse
    {
        return $this->sendMapped('/shipment', $request->toArray(), [CreateShipmentResponse::class, 'fromArray']);
    }

    public function cancel(CancelShipmentRequest $request): void
    {
        $this->sendWithoutResult('/shipment/cancel', $request->toArray());
    }

    public function cancelWithDelete(CancelShipmentRequest $request): void
    {
        $this->sendWithoutResult('/shipment', $request->toArray(), method: 'DELETE');
    }

    public function addParcel(AddParcelRequest $request): AddParcelResponse
    {
        return $this->sendMapped('/shipment/add_parcel', $request->toArray(), [AddParcelResponse::class, 'fromArray']);
    }

    public function finalizePendingShipment(FinalizePendingShipmentRequest $request): CreateShipmentResponse
    {
        return $this->sendMapped('/shipment/finalize', $request->toArray(), [CreateShipmentResponse::class, 'fromArray']);
    }

    public function info(ShipmentInformationRequest $request): ShipmentInformationResponse
    {
        return $this->sendMapped('/shipment/info', $request->toArray(), [ShipmentInformationResponse::class, 'fromArray']);
    }

    public function secondaryShipments(SecondaryShipmentsRequest $request): SecondaryShipmentsResponse
    {
        return $this->sendMapped(
            '/shipment/{shipmentId}/secondary',
            $request->toArray(),
            [SecondaryShipmentsResponse::class, 'fromArray'],
            ['shipmentId' => $request->shipmentId],
        );
    }

    public function update(UpdateShipmentRequest $request): CreateShipmentResponse
    {
        return $this->sendMapped('/shipment/update', $request->toArray(), [CreateShipmentResponse::class, 'fromArray']);
    }

    public function updateProperties(UpdateShipmentPropertiesRequest $request): CreateShipmentResponse
    {
        return $this->sendMapped('/shipment/update/properties', $request->toArray(), [CreateShipmentResponse::class, 'fromArray']);
    }

    public function findParcelsByReference(FindParcelsByReferenceRequest $request): FindParcelsByReferenceResponse
    {
        return $this->sendMapped('/shipment/search', $request->toArray(), [FindParcelsByReferenceResponse::class, 'fromArray']);
    }

    public function handoverToCourier(HandoverToCourierRequest $request): void
    {
        $this->sendWithoutResult('/shipment/handover', $request->toArray());
    }

    public function handoverToMidwayCarrier(HandoverToMidwayCarrierRequest $request): void
    {
        $this->sendWithoutResult('/shipment/handover-to-midway-carrier', $request->toArray());
    }

    public function barcodeInformation(BarcodeInformationRequest|ShipmentParcelRef $request): BarcodeInformationResponse
    {
        $resolvedRequest = $request instanceof BarcodeInformationRequest
            ? $request
            : new BarcodeInformationRequest($request);

        return $this->sendMapped('/shipment/barcode-information', $resolvedRequest->toArray(), [BarcodeInformationResponse::class, 'fromArray']);
    }

    /**
     * @template TResponse
     *
     * @param array<string, mixed> $payload
     * @param callable(array<string, mixed>): TResponse $mapper
     * @param array<string, string|int> $pathParameters
     *
     * @return TResponse
     */
    private function sendMapped(string $path, array $payload, callable $mapper, array $pathParameters = []): mixed
    {
        return $mapper($this->client->sendJson(ApiRequest::json('POST', $path, $payload, $pathParameters)));
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string|int> $pathParameters
     */
    private function sendWithoutResult(string $path, array $payload, array $pathParameters = [], string $method = 'POST'): void
    {
        $this->client->sendJson(ApiRequest::json($method, $path, $payload, $pathParameters));
    }
}
