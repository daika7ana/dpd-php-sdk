<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Pickup\Responses\PickupOrder;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupRequest;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupTermsRequest;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Http\ApiRequest;

final readonly class PickupResource
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @return Collection<int, PickupOrder>
     */
    public function order(PickupRequest $request): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/pickup', $request->toArray()));
        $rawOrders = $data['orders'] ?? null;

        if (!is_array($rawOrders)) {
            throw new UnsupportedResponseException(
                message: 'Expected an orders array in the DPD response.',
                responseDetails: ['payload' => $data],
            );
        }

        $orders = array_map(
            static fn(array $order): PickupOrder => PickupOrder::fromArray($order),
            array_values(array_filter($rawOrders, static fn(mixed $order): bool => is_array($order))),
        );

        return new Collection($orders);
    }

    /**
     * @return Collection<int, string>
     */
    public function terms(PickupTermsRequest $request): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/pickup/terms', $request->toArray()));
        $rawCutoffs = $data['cutoffs'] ?? null;

        if (!is_array($rawCutoffs)) {
            throw new UnsupportedResponseException(
                message: 'Expected a cutoffs array in the DPD response.',
                responseDetails: ['payload' => $data],
            );
        }

        return new Collection(array_map(
            static fn(mixed $cutoff): string => (string) $cutoff,
            array_values(array_filter($rawCutoffs, static fn(mixed $cutoff): bool => is_scalar($cutoff))),
        ));
    }
}
