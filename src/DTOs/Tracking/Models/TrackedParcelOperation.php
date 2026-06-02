<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

use Daika7ana\Dpd\DTOs\Tracking\Resolved\ResolvedTrackingExceptionCode;
use Daika7ana\Dpd\DTOs\Tracking\Resolved\ResolvedTrackingOperationCode;
use Daika7ana\Dpd\Support\TrackingExceptionCodeResolver;
use Daika7ana\Dpd\Support\TrackingOperationCodeResolver;

final readonly class TrackedParcelOperation
{
    /**
     * @param list<int> $exceptionCodes
     */
    public function __construct(
        public string $dateTime,
        public int $operationCode,
        public string $description,
        public array $exceptionCodes = [],
        public ?string $place = null,
        public ?string $comment = null,
        public ?string $returnShipmentId = null,
        public ?string $redirectShipmentId = null,
        public ?string $consignee = null,
        public ?string $podImageUrl = null,
        public ?TrackedParcelOperationAdditionalInfo $additionalInfo = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $exceptionCodes = array_map(
            static fn(mixed $code): int => (int) $code,
            array_values(array_filter($data['exceptionCodes'] ?? [], static fn(mixed $code): bool => is_scalar($code))),
        );

        return new self(
            dateTime: (string) ($data['dateTime'] ?? ''),
            operationCode: (int) ($data['operationCode'] ?? 0),
            description: (string) ($data['description'] ?? ''),
            exceptionCodes: $exceptionCodes,
            place: isset($data['place']) ? (string) $data['place'] : null,
            comment: isset($data['comment']) ? (string) $data['comment'] : null,
            returnShipmentId: isset($data['returnShipmentId']) ? (string) $data['returnShipmentId'] : null,
            redirectShipmentId: isset($data['redirectShipmentId']) ? (string) $data['redirectShipmentId'] : null,
            consignee: isset($data['consignee']) ? (string) $data['consignee'] : null,
            podImageUrl: isset($data['podImageURL']) ? (string) $data['podImageURL'] : null,
            additionalInfo: is_array($data['additionalInfo'] ?? null)
                ? TrackedParcelOperationAdditionalInfo::fromArray($data['additionalInfo'])
                : null,
        );
    }

    public function resolvedOperationCode(): ResolvedTrackingOperationCode
    {
        return TrackingOperationCodeResolver::resolve($this->operationCode);
    }

    /**
     * @return list<ResolvedTrackingExceptionCode>
     */
    public function resolvedExceptionCodes(): array
    {
        return array_map(
            static fn(int $code): ResolvedTrackingExceptionCode => TrackingExceptionCodeResolver::resolve($code),
            $this->exceptionCodes,
        );
    }
}
