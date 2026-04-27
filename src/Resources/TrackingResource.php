<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Tracking\Responses\BulkTrackingDataFilesResponse;
use Daika7ana\Dpd\DTOs\Tracking\Responses\TrackResponse;
use Daika7ana\Dpd\DTOs\Tracking\Models\TrackShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Tracking\Requests\BulkTrackingDataFilesRequest;
use Daika7ana\Dpd\DTOs\Tracking\Requests\TrackRequest;
use Daika7ana\Dpd\Http\ApiRequest;
use Daika7ana\Dpd\Support\BatchingHelper;

final readonly class TrackingResource
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @param list<TrackShipmentParcelRef|ShipmentParcelRef>|TrackRequest $request
     */
    public function track(TrackRequest|array $request, ?bool $lastOperationOnly = null): TrackResponse
    {
        $resolvedRequest = $this->resolveTrackRequest($request, $lastOperationOnly);

        return TrackResponse::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/track', $resolvedRequest->toArray())),
        );
    }

    /**
     * @param list<TrackShipmentParcelRef|ShipmentParcelRef>|TrackRequest $request
     */
    public function trackBatched(TrackRequest|array $request, ?bool $lastOperationOnly = null, int $batchSize = 10): TrackResponse
    {
        $resolvedRequest = $this->resolveTrackRequest($request, $lastOperationOnly);
        $mergedResponse = new TrackResponse(new \Daika7ana\Dpd\DTOs\Common\Support\Collection([]));

        foreach (BatchingHelper::chunk($resolvedRequest->parcels, $batchSize) as $batch) {
            $batchResponse = $this->track(new TrackRequest($batch, $resolvedRequest->lastOperationOnly));
            $mergedResponse = $mergedResponse->merge($batchResponse);
        }

        return $mergedResponse;
    }

    public function bulkDataFiles(BulkTrackingDataFilesRequest|int $request): BulkTrackingDataFilesResponse
    {
        $resolvedRequest = $request instanceof BulkTrackingDataFilesRequest
            ? $request
            : new BulkTrackingDataFilesRequest($request);

        return BulkTrackingDataFilesResponse::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/track/bulk', $resolvedRequest->toArray())),
        );
    }

    /**
     * @param list<TrackShipmentParcelRef|ShipmentParcelRef>|TrackRequest $request
     */
    private function resolveTrackRequest(TrackRequest|array $request, ?bool $lastOperationOnly): TrackRequest
    {
        if ($request instanceof TrackRequest) {
            return $request;
        }

        $parcels = array_map(
            static function (TrackShipmentParcelRef|ShipmentParcelRef $parcel): TrackShipmentParcelRef {
                return $parcel instanceof TrackShipmentParcelRef
                    ? $parcel
                    : TrackShipmentParcelRef::fromParcelRef($parcel);
            },
            $request,
        );

        return new TrackRequest($parcels, $lastOperationOnly);
    }
}
