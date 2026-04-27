<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Clients\Contracts;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class CodAdditionalServiceContractInfo
{
    /**
     * @param Collection<int, CodInternationalAnnexContractInfo> $internationalCodAnnexes
     */
    public function __construct(
        public bool $moneyTransferAllowed,
        public bool $codFiscalReceiptAllowed,
        public bool $hasCodAnnex,
        public Collection $internationalCodAnnexes,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $annexes = array_map(
            static fn(array $annex): CodInternationalAnnexContractInfo => CodInternationalAnnexContractInfo::fromArray($annex),
            array_values(array_filter($data['internationalCODAnnexes'] ?? [], static fn(mixed $annex): bool => is_array($annex))),
        );

        return new self(
            moneyTransferAllowed: (bool) ($data['moneyTransferAllowed'] ?? false),
            codFiscalReceiptAllowed: (bool) ($data['codFiscalReceiptAllowed'] ?? false),
            hasCodAnnex: (bool) ($data['hasCODAnnex'] ?? false),
            internationalCodAnnexes: new Collection($annexes),
        );
    }
}
