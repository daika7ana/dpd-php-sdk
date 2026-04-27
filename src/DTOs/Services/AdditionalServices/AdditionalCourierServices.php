<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Services\AdditionalServices;

final readonly class AdditionalCourierServices
{
    public function __construct(
        public ?AdditionalCourierService $cod = null,
        public ?AdditionalCourierService $obpd = null,
        public ?AdditionalCourierService $declaredValue = null,
        public ?AdditionalCourierService $fixedTimeDelivery = null,
        public ?AdditionalCourierService $specialDelivery = null,
        public ?AdditionalCourierService $deliveryToFloor = null,
        public ?AdditionalCourierService $rod = null,
        public ?AdditionalCourierService $returnReceipt = null,
        public ?AdditionalCourierService $swap = null,
        public ?AdditionalCourierService $rop = null,
        public ?AdditionalCourierService $returnVoucher = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            cod: self::mapOptionalService($data['cod'] ?? null),
            obpd: self::mapOptionalService($data['obpd'] ?? null),
            declaredValue: self::mapOptionalService($data['declaredValue'] ?? null),
            fixedTimeDelivery: self::mapOptionalService($data['fixedTimeDelivery'] ?? null),
            specialDelivery: self::mapOptionalService($data['specialDelivery'] ?? null),
            deliveryToFloor: self::mapOptionalService($data['deliveryToFloor'] ?? null),
            rod: self::mapOptionalService($data['rod'] ?? null),
            returnReceipt: self::mapOptionalService($data['returnReceipt'] ?? null),
            swap: self::mapOptionalService($data['swap'] ?? null),
            rop: self::mapOptionalService($data['rop'] ?? null),
            returnVoucher: self::mapOptionalService($data['returnVoucher'] ?? null),
        );
    }

    private static function mapOptionalService(mixed $value): ?AdditionalCourierService
    {
        if (!is_array($value)) {
            return null;
        }

        return AdditionalCourierService::fromArray($value);
    }
}
