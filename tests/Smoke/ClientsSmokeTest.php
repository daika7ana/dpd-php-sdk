<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

use Daika7ana\Dpd\DTOs\Clients\Records\ClientRecord;
use Daika7ana\SimpleCollections\Collection;

final class ClientsSmokeTest extends SmokeTestCase
{
    public function testCanFetchOwnClientIdFromLiveApi(): void
    {
        $clientId = $this->client()->clients()->ownClientId();

        $this->assertGreaterThan(0, $clientId);
    }

    public function testCanFetchOwnClientRecordFromLiveApi(): void
    {
        $client = $this->client();
        $clientId = $client->clients()->ownClientId();
        $record = $client->clients()->get($clientId);

        $this->assertInstanceOf(ClientRecord::class, $record);
        $this->assertSame($clientId, $record->clientId);
    }

    public function testCanFetchContractClientsFromLiveApi(): void
    {
        $clients = $this->client()->clients()->contractClients();

        $this->assertInstanceOf(Collection::class, $clients);

        foreach ($clients as $record) {
            $this->assertInstanceOf(ClientRecord::class, $record);
            $this->assertGreaterThan(0, $record->clientId);
        }
    }

    public function testCanFetchContractInfoFromLiveApi(): void
    {
        $contractInfo = $this->client()->clients()->contractInfo();

        $this->assertGreaterThan(0, $contractInfo->id);
    }
}
