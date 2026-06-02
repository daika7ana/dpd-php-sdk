<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Clients\Contracts\ContractInfo;
use Daika7ana\Dpd\DTOs\Clients\Contracts\CodAdditionalServiceContractInfo;
use Daika7ana\Dpd\DTOs\Clients\Contracts\SpecialDeliveryRequirements;
use Daika7ana\Dpd\DTOs\Clients\Contracts\CodInternationalAnnexContractInfo;
use Daika7ana\Dpd\DTOs\Clients\Contracts\Requirement;
use Daika7ana\Dpd\DTOs\Clients\Records\ClientRecord;
use PHPUnit\Framework\TestCase;

final class ClientContractsTest extends TestCase
{
    public function testContractInfoMapsAllFields(): void
    {
        $data = [
            'id' => 42,
            'administrativeFeeAllowed' => true,
            'specialDeliveryRequirements' => [
                'requiredForAllShipments' => true,
                'requirements' => [
                    ['specialDeliveryId' => 7, 'name' => 'Saturday delivery'],
                ],
            ],
            'cod' => [
                'moneyTransferAllowed' => true,
                'codFiscalReceiptAllowed' => false,
                'hasCODAnnex' => true,
                'internationalCODAnnexes' => [
                    ['countryId' => 642, 'annexNumber' => 'A-001'],
                ],
            ],
        ];

        $contract = ContractInfo::fromArray($data);

        $this->assertSame(42, $contract->id);
        $this->assertTrue($contract->administrativeFeeAllowed);
        $this->assertNotNull($contract->specialDeliveryRequirements);
        $this->assertTrue($contract->specialDeliveryRequirements->requiredForAllShipments);
        $this->assertCount(1, $contract->specialDeliveryRequirements->requirements);
        $this->assertNotNull($contract->cod);
        $this->assertTrue($contract->cod->moneyTransferAllowed);
        $this->assertFalse($contract->cod->codFiscalReceiptAllowed);
        $this->assertTrue($contract->cod->hasCodAnnex);
        $this->assertCount(1, $contract->cod->internationalCodAnnexes);
    }

    public function testContractInfoHandlesMinimalData(): void
    {
        $contract = ContractInfo::fromArray(['id' => 1]);

        $this->assertSame(1, $contract->id);
        $this->assertFalse($contract->administrativeFeeAllowed);
        $this->assertNull($contract->specialDeliveryRequirements);
        $this->assertNull($contract->cod);
    }

    public function testCodAdditionalServiceContractInfoMapsFields(): void
    {
        $data = [
            'moneyTransferAllowed' => true,
            'codFiscalReceiptAllowed' => true,
            'hasCODAnnex' => false,
            'internationalCODAnnexes' => [
                ['countryId' => 642, 'annexNumber' => 'A-001'],
                ['countryId' => 744, 'annexNumber' => 'A-002'],
            ],
        ];

        $cod = CodAdditionalServiceContractInfo::fromArray($data);

        $this->assertTrue($cod->moneyTransferAllowed);
        $this->assertTrue($cod->codFiscalReceiptAllowed);
        $this->assertFalse($cod->hasCodAnnex);
        $this->assertCount(2, $cod->internationalCodAnnexes);
        $this->assertInstanceOf(CodInternationalAnnexContractInfo::class, $cod->internationalCodAnnexes->first());
    }

    public function testCodAdditionalServiceContractInfoHandlesEmptyData(): void
    {
        $cod = CodAdditionalServiceContractInfo::fromArray([]);

        $this->assertFalse($cod->moneyTransferAllowed);
        $this->assertFalse($cod->codFiscalReceiptAllowed);
        $this->assertFalse($cod->hasCodAnnex);
        $this->assertCount(0, $cod->internationalCodAnnexes);
    }

    public function testSpecialDeliveryRequirementsMapsFields(): void
    {
        $data = [
            'requiredForAllShipments' => true,
            'requirements' => [
                ['id' => 7, 'text' => 'Saturday delivery'],
                ['id' => 12, 'text' => 'Express'],
            ],
        ];

        $requirements = SpecialDeliveryRequirements::fromArray($data);

        $this->assertTrue($requirements->requiredForAllShipments);
        $this->assertCount(2, $requirements->requirements);
        $this->assertInstanceOf(Requirement::class, $requirements->requirements->first());
    }

    public function testSpecialDeliveryRequirementsFiltersNonArrayEntries(): void
    {
        $data = [
            'requiredForAllShipments' => false,
            'requirements' => [
                ['specialDeliveryId' => 7],
                'invalid',
                null,
            ],
        ];

        $requirements = SpecialDeliveryRequirements::fromArray($data);

        $this->assertCount(1, $requirements->requirements);
    }

    public function testClientRecordMapsFields(): void
    {
        $data = [
            'clientId' => 100,
            'clientName' => 'Test SRL',
            'address' => ['countryId' => 642, 'siteId' => 68134],
        ];

        $client = ClientRecord::fromArray($data);

        $this->assertSame(100, $client->clientId);
        $this->assertSame('Test SRL', $client->clientName);
        $this->assertNotNull($client->address);
    }

    public function testClientRecordHandlesMinimalData(): void
    {
        $client = ClientRecord::fromArray([]);

        $this->assertSame(0, $client->clientId);
        $this->assertSame('', $client->clientName);
        $this->assertNull($client->address);
    }
}
