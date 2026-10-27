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
     * @return Collection<int, Collection<int, CalculationResult>>
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

        $calculationGroups = [];

        foreach ($rawCalculations as $rawGroup) {
            if (!is_array($rawGroup)) {
                continue;
            }

            $rawRows = array_key_exists('serviceId', $rawGroup)
                ? [$rawGroup]
                : array_values(array_filter($rawGroup, static fn(mixed $row): bool => is_array($row)));

            $rows = array_map(
                static fn(array $row): CalculationResult => CalculationResult::fromArray($row),
                $rawRows,
            );

            $calculationGroups[] = new Collection($rows);
        }

        return new Collection($calculationGroups);
    }
}
