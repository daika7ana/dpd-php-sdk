<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Shipments\Responses\ShipmentRecord;
use PHPUnit\Framework\TestCase;

final class ShipmentRecordTest extends TestCase
{
    public function testFromArrayMapsIdAndOptionalFields(): void
    {
        $data = [
            'id' => 'SHIP-001',
            'sender' => ['clientId' => 10, 'clientName' => 'Sender SRL'],
            'recipient' => ['clientId' => 20, 'clientName' => 'Recipient SRL'],
            'service' => ['serviceId' => 2505],
            'content' => ['parcelsCount' => 2, 'declaredWeight' => 5.0],
            'payment' => ['payer' => 'SENDER'],
            'shipmentNote' => 'Fragile',
            'ref1' => 'REF-001',
            'ref2' => 'REF-002',
            'price' => ['amount' => 10.0, 'vat' => 1.9, 'total' => 11.9, 'currency' => 'RON', 'amountLocal' => 10.0, 'vatLocal' => 1.9, 'totalLocal' => 11.9, 'currencyLocal' => 'RON', 'currencyExchangeRateUnit' => 1, 'currencyExchangeRate' => 1.0],
            'returnShipmentId' => 'RET-001',
            'redirectShipmentId' => 'REDIR-001',
            'pendingShipment' => false,
        ];

        $record = ShipmentRecord::fromArray($data);

        $this->assertSame('SHIP-001', $record->id);
        $this->assertNotNull($record->sender);
        $this->assertSame('Sender SRL', $record->sender->clientName);
        $this->assertNotNull($record->recipient);
        $this->assertSame('Recipient SRL', $record->recipient->clientName);
        $this->assertNotNull($record->service);
        $this->assertSame(2505, $record->service->serviceId);
        $this->assertNotNull($record->content);
        $this->assertSame(2, $record->content->parcelsCount);
        $this->assertNotNull($record->payment);
        $this->assertSame('Fragile', $record->shipmentNote);
        $this->assertSame('REF-001', $record->ref1);
        $this->assertSame('REF-002', $record->ref2);
        $this->assertNotNull($record->price);
        $this->assertSame(11.9, $record->price->total);
        $this->assertSame('RET-001', $record->returnShipmentId);
        $this->assertSame('REDIR-001', $record->redirectShipmentId);
        $this->assertFalse($record->pendingShipment);
    }

    public function testFromArrayHandlesMinimalData(): void
    {
        $data = ['id' => 'SHIP-002'];

        $record = ShipmentRecord::fromArray($data);

        $this->assertSame('SHIP-002', $record->id);
        $this->assertNull($record->sender);
        $this->assertNull($record->recipient);
        $this->assertNull($record->service);
        $this->assertNull($record->content);
        $this->assertNull($record->payment);
        $this->assertNull($record->shipmentNote);
        $this->assertNull($record->ref1);
        $this->assertNull($record->ref2);
        $this->assertNull($record->price);
        $this->assertNull($record->delivery);
        $this->assertNull($record->primaryShipment);
        $this->assertNull($record->returnShipmentId);
        $this->assertNull($record->redirectShipmentId);
        $this->assertNull($record->pendingShipment);
    }

    public function testFromArrayHandlesNonArrayNestedFields(): void
    {
        $data = [
            'id' => 'SHIP-003',
            'sender' => 'not-an-array',
            'recipient' => null,
            'price' => 'not-an-array',
        ];

        $record = ShipmentRecord::fromArray($data);

        $this->assertNull($record->sender);
        $this->assertNull($record->recipient);
        $this->assertNull($record->price);
    }
}
