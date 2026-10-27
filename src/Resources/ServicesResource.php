<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Calculations\Results\ExtendedCourierService;
use Daika7ana\Dpd\DTOs\Calculations\Requests\DestinationServicesRequest;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Services\Catalog\CourierService;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Http\ApiRequest;
use DateTimeInterface;

final class ServicesResource
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @return Collection<int, CourierService>
     */
    public function list(?DateTimeInterface $date = null): Collection
    {
        $payload = [];

        if ($date !== null) {
            $payload['date'] = $date->format('Y-m-d');
        }

        $data = $this->client->sendJson(ApiRequest::json('POST', '/services', $payload));
        $rawServices = $data['services'] ?? null;

        if (!is_array($rawServices)) {
            throw new UnsupportedResponseException(
                message: 'Expected a services array in the DPD response.',
                responseDetails: ['resultKeys' => array_keys($data)],
            );
        }

        $services = [];

        foreach ($rawServices as $rawService) {
            if (!is_array($rawService)) {
                continue;
            }

            $services[] = CourierService::fromArray($rawService);
        }

        return new Collection($services);
    }

    /**
     * @return Collection<int, ExtendedCourierService>
     */
    public function destination(DestinationServicesRequest $request): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/services/destination', $request->toArray()));
        $rawServices = $data['services'] ?? null;

        if (!is_array($rawServices)) {
            throw new UnsupportedResponseException(
                message: 'Expected a destination services array in the DPD response.',
                responseDetails: ['resultKeys' => array_keys($data)],
            );
        }

        $services = [];

        foreach ($rawServices as $rawService) {
            if (!is_array($rawService)) {
                continue;
            }

            $services[] = ExtendedCourierService::fromArray($rawService);
        }

        return new Collection($services);
    }
}
