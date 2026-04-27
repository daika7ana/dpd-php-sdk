<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;

final readonly class TrackedParcel
{
    /**
     * @param Collection<int, string> $externalCarrierParcelNumbers
     * @param Collection<int, TrackedParcelOperation> $operations
     * @param array<string, ExternalCarrierParcelNumberDetails> $externalCarrierParcelNumbersDetails
     */
    public function __construct(
        public string $parcelId,
        public Collection $externalCarrierParcelNumbers,
        public Collection $operations,
        public array $externalCarrierParcelNumbersDetails,
        public ?DpdError $error = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $operationItems = array_map(
            static fn(array $operation): TrackedParcelOperation => TrackedParcelOperation::fromArray($operation),
            array_values(array_filter($data['operations'] ?? [], static fn(mixed $operation): bool => is_array($operation))),
        );

        $detailItems = [];
        foreach (($data['externalCarrierParcelNumbersDetails'] ?? []) as $key => $value) {
            if (!is_array($value)) {
                continue;
            }

            $detailItems[(string) $key] = ExternalCarrierParcelNumberDetails::fromArray($value);
        }

        $externalCarrierParcelNumbers = array_map(
            static fn(mixed $value): string => (string) $value,
            array_values(array_filter($data['externalCarrierParcelNumbers'] ?? [], static fn(mixed $value): bool => is_scalar($value))),
        );

        return new self(
            parcelId: (string) ($data['parcelId'] ?? ''),
            externalCarrierParcelNumbers: new Collection($externalCarrierParcelNumbers),
            operations: new Collection($operationItems),
            externalCarrierParcelNumbersDetails: $detailItems,
            error: is_array($data['error'] ?? null) ? DpdError::fromArray($data['error']) : null,
        );
    }
}
