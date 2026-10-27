<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Payments\Payouts\Payout;
use Daika7ana\Dpd\DTOs\Payments\Requests\PayoutRequest;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Http\ApiRequest;

final readonly class PaymentsResource
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @return Collection<int, Payout>
     */
    public function payouts(PayoutRequest $request): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/payments', $request->toArray()));
        $rawPayouts = $data['payouts'] ?? null;

        if (!is_array($rawPayouts)) {
            throw new UnsupportedResponseException(
                message: 'Expected a payouts array in the DPD response.',
                responseDetails: ['resultKeys' => array_keys($data)],
            );
        }

        $payouts = array_map(
            static fn(array $payout): Payout => Payout::fromArray($payout),
            array_values(array_filter($rawPayouts, static fn(mixed $payout): bool => is_array($payout))),
        );

        return new Collection($payouts);
    }
}
