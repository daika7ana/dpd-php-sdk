<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentReturnAdditionalServices
{
    public function __construct(
        public ?ShipmentRodAdditionalService $rod = null,
        public ?ShipmentReturnReceiptAdditionalService $returnReceipt = null,
        public ?ShipmentElectronicReturnReceiptAdditionalService $electronicReturnReceipt = null,
        public ?ShipmentSwapAdditionalService $swap = null,
        public ?ShipmentRopAdditionalService $rop = null,
        public ?ShipmentReturnVoucherAdditionalService $returnVoucher = null,
        public ?int $sendBackClientId = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|int>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'rod' => $this->rod?->toArray(),
            'returnReceipt' => $this->returnReceipt?->toArray(),
            'electronicReturnReceipt' => $this->electronicReturnReceipt?->toArray(),
            'swap' => $this->swap?->toArray(),
            'rop' => $this->rop?->toArray(),
            'returnVoucher' => $this->returnVoucher?->toArray(),
            'sendBackClientId' => $this->sendBackClientId,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rod: isset($data['rod']) && is_array($data['rod']) ? ShipmentRodAdditionalService::fromArray($data['rod']) : null,
            returnReceipt: isset($data['returnReceipt']) && is_array($data['returnReceipt']) ? ShipmentReturnReceiptAdditionalService::fromArray($data['returnReceipt']) : null,
            electronicReturnReceipt: isset($data['electronicReturnReceipt']) && is_array($data['electronicReturnReceipt']) ? ShipmentElectronicReturnReceiptAdditionalService::fromArray($data['electronicReturnReceipt']) : null,
            swap: isset($data['swap']) && is_array($data['swap']) ? ShipmentSwapAdditionalService::fromArray($data['swap']) : null,
            rop: isset($data['rop']) && is_array($data['rop']) ? ShipmentRopAdditionalService::fromArray($data['rop']) : null,
            returnVoucher: isset($data['returnVoucher']) && is_array($data['returnVoucher']) ? ShipmentReturnVoucherAdditionalService::fromArray($data['returnVoucher']) : null,
            sendBackClientId: isset($data['sendBackClientId']) ? (int) $data['sendBackClientId'] : null,
        );
    }
}
