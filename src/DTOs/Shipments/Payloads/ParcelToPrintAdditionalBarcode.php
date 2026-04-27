<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use Daika7ana\Dpd\Enums\Printing\AdditionalBarcodeValueType;
use Daika7ana\Dpd\Enums\Printing\BarcodeFormat;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ParcelToPrintAdditionalBarcode
{
    public function __construct(
        public ?string $value = null,
        public ?string $label = null,
        public ?BarcodeFormat $format = null,
        public ?AdditionalBarcodeValueType $valueType = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'value' => $this->value,
            'label' => $this->label,
            'format' => $this->format?->value,
            'valueType' => $this->valueType?->value,
        ]);
    }
}
