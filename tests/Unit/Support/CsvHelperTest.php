<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\Support;

use Daika7ana\Dpd\Support\CsvHelper;
use PHPUnit\Framework\TestCase;

final class CsvHelperTest extends TestCase
{
    public function testReadAssociativeRowsParsesNormalCsv(): void
    {
        $csv = "id,name,country\n1,Office A,RO\n2,Office B,BG\n";

        $rows = CsvHelper::readAssociativeRows($csv);

        $this->assertCount(2, $rows);
        $this->assertSame('1', $rows[0]['id']);
        $this->assertSame('Office A', $rows[0]['name']);
        $this->assertSame('RO', $rows[0]['country']);
        $this->assertSame('2', $rows[1]['id']);
        $this->assertSame('Office B', $rows[1]['name']);
        $this->assertSame('BG', $rows[1]['country']);
    }

    public function testReadAssociativeRowsHandlesEmptyInput(): void
    {
        $rows = CsvHelper::readAssociativeRows('');

        $this->assertSame([], $rows);
    }

    public function testReadAssociativeRowsStripsUtf8Bom(): void
    {
        $csv = "\xEF\xBB\xBFid,name\n1,Test\n";

        $rows = CsvHelper::readAssociativeRows($csv);

        $this->assertCount(1, $rows);
        $this->assertSame('1', $rows[0]['id']);
        $this->assertSame('Test', $rows[0]['name']);
    }

    public function testReadAssociativeRowsSkipsEmptyRows(): void
    {
        $csv = "id,name\n1,Test\n\n  , \n2,Another\n";

        $rows = CsvHelper::readAssociativeRows($csv);

        $this->assertCount(2, $rows);
        $this->assertSame('1', $rows[0]['id']);
        $this->assertSame('2', $rows[1]['id']);
    }

    public function testReadAssociativeRowsTrimsWhitespace(): void
    {
        $csv = " id , name \n 1 , Test \n";

        $rows = CsvHelper::readAssociativeRows($csv);

        $this->assertSame('id', array_keys($rows[0])[0]);
        $this->assertSame('1', $rows[0]['id']);
        $this->assertSame('Test', $rows[0]['name']);
    }

    public function testSplitListReturnsNullAsEmptyArray(): void
    {
        $this->assertSame([], CsvHelper::splitList(null));
    }

    public function testSplitListReturnsEmptyStringAsEmptyArray(): void
    {
        $this->assertSame([], CsvHelper::splitList(''));
    }

    public function testSplitListReturnsEmptyStringWhenPreservingEmpty(): void
    {
        $this->assertSame([''], CsvHelper::splitList('', preserveEmpty: true));
    }

    public function testSplitListSplitsCommaSeparatedValues(): void
    {
        $result = CsvHelper::splitList('a,b,c');

        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function testSplitListTrimsValues(): void
    {
        $result = CsvHelper::splitList(' a , b , c ');

        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function testSplitListFiltersEmptyByDefault(): void
    {
        $result = CsvHelper::splitList('a,,b,,c');

        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function testSplitListPreservesEmptyWhenRequested(): void
    {
        $result = CsvHelper::splitList('a,,b,,c', preserveEmpty: true);

        $this->assertSame(['a', '', 'b', '', 'c'], $result);
    }

    public function testToBoolReturnsFalseForNull(): void
    {
        $this->assertFalse(CsvHelper::toBool(null));
    }

    public function testToBoolReturnsFalseForEmptyString(): void
    {
        $this->assertFalse(CsvHelper::toBool(''));
    }

    public function testToBoolReturnsTrueForTruthyValues(): void
    {
        $this->assertTrue(CsvHelper::toBool('1'));
        $this->assertTrue(CsvHelper::toBool('true'));
        $this->assertTrue(CsvHelper::toBool('TRUE'));
        $this->assertTrue(CsvHelper::toBool('y'));
        $this->assertTrue(CsvHelper::toBool('YES'));
    }

    public function testToBoolReturnsFalseForFalsyValues(): void
    {
        $this->assertFalse(CsvHelper::toBool('0'));
        $this->assertFalse(CsvHelper::toBool('false'));
        $this->assertFalse(CsvHelper::toBool('no'));
        $this->assertFalse(CsvHelper::toBool('anything'));
    }
}
