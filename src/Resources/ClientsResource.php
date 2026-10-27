<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Clients\Records\ClientRecord;
use Daika7ana\Dpd\DTOs\Clients\Contracts\ContractInfo;
use Daika7ana\Dpd\DTOs\Clients\Requests\CreateContactRequest;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Http\ApiRequest;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ClientsResource
{
    public function __construct(
        private Client $client,
    ) {}

    public function get(int $clientId): ?ClientRecord
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/client/{id}', [], ['id' => $clientId]));
        $rawClient = $data['client'] ?? null;

        return is_array($rawClient) ? ClientRecord::fromArray($rawClient) : null;
    }

    /**
     * @return Collection<int, ClientRecord>
     */
    public function contractClients(): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/client/contract', []));
        $rawClients = $data['clients'] ?? null;

        if (!is_array($rawClients)) {
            throw new UnsupportedResponseException(
                message: 'Expected a clients array in the DPD response.',
                responseDetails: ['resultKeys' => array_keys($data)],
            );
        }

        $clients = array_map(
            static fn(array $client): ClientRecord => ClientRecord::fromArray($client),
            array_values(array_filter($rawClients, static fn(mixed $client): bool => is_array($client))),
        );

        return new Collection($clients);
    }

    public function createContact(CreateContactRequest $request): int
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/client/contact', $request->toArray()));

        return $this->requireClientId($data);
    }

    public function contactByExternalId(string $externalId, ?string $secretKey = null): ?ClientRecord
    {
        $payload = PayloadFilter::removeNulls(['secretKey' => $secretKey]);
        $data = $this->client->sendJson(ApiRequest::json('POST', '/client/contact/external/{id}', $payload, ['id' => $externalId]));
        $rawClient = $data['client'] ?? null;

        return is_array($rawClient) ? ClientRecord::fromArray($rawClient) : null;
    }

    public function ownClientId(): int
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', '/client', []));

        return $this->requireClientId($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function requireClientId(array $data): int
    {
        if (!isset($data['clientId']) || !is_numeric($data['clientId'])) {
            throw new UnsupportedResponseException(
                message: 'Expected a numeric clientId in the DPD response.',
                responseDetails: ['resultKeys' => array_keys($data)],
            );
        }

        return (int) $data['clientId'];
    }

    public function contractInfo(): ContractInfo
    {
        return ContractInfo::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/client/contract/info', [])),
        );
    }
}
