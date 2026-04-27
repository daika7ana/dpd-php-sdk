<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Support;

final class CsvHelper
{
    private function __construct() {}

    /**
     * @return list<array<string, string>>
     */
    public static function readAssociativeRows(string $contents): array
    {
        $stream = fopen('php://temp', 'r+');

        if ($stream === false) {
            return [];
        }

        fwrite($stream, self::stripUtf8Bom($contents));
        rewind($stream);

        $header = fgetcsv($stream, separator: ',', enclosure: '"', escape: '\\');

        if ($header === false) {
            fclose($stream);

            return [];
        }

        $normalizedHeader = array_map(
            static fn(string|int|float|bool|null $value): string => trim((string) $value),
            $header,
        );

        $rows = [];

        while (($row = fgetcsv($stream, separator: ',', enclosure: '"', escape: '\\')) !== false) {
            if ($row === [null] || self::rowIsEmpty($row)) {
                continue;
            }

            $values = [];

            foreach ($normalizedHeader as $index => $column) {
                $values[$column] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            }

            $rows[] = $values;
        }

        fclose($stream);

        return $rows;
    }

    /**
     * @return list<string>
     */
    public static function splitList(?string $value, bool $preserveEmpty = false): array
    {
        if ($value === null) {
            return [];
        }

        if ($value === '') {
            return $preserveEmpty ? [''] : [];
        }

        $items = array_map('trim', explode(',', $value));

        if ($preserveEmpty) {
            return array_values($items);
        }

        return array_values(array_filter($items, static fn(string $item): bool => $item !== ''));
    }

    public static function toBool(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return in_array(strtolower($value), ['1', 'true', 'y', 'yes'], true);
    }

    private static function stripUtf8Bom(string $contents): string
    {
        if (str_starts_with($contents, "\xEF\xBB\xBF")) {
            return substr($contents, 3);
        }

        return $contents;
    }

    /**
     * @param list<string|int|float|bool|null> $row
     */
    private static function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
