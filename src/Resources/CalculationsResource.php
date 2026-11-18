<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Calculations\Results\CalculationResult;
use Daika7ana\Dpd\DTOs\Calculations\Requests\CalculationRequest;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Http\ApiRequest;

final readonly class CalculationsResource
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @return Collection<int, CalculationResult>
     */
    public function calculate(CalculationRequest $request): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/calculate', $request->toArray()));
        $rawCalculations = $data['calculations'] ?? null;

        if (!is_array($rawCalculations)) {
            throw new UnsupportedResponseException(
                message: 'Expected a calculations array in the DPD response.',
                responseDetails: ['resultKeys' => array_keys($data)],
            );
        }

        $results = [];

        foreach ($rawCalculations as $rawResult) {
            if (is_array($rawResult)) {
                $results[] = CalculationResult::fromArray($rawResult);
            }
        }

        return new Collection($results);
    }
}
