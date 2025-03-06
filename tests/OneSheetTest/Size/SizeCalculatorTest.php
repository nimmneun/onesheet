<?php

namespace OneSheetTest\Size;

use OneSheet\Size\SizeCalculator;
use PHPUnit\Framework\TestCase;

class SizeCalculatorTest extends TestCase
{
    private static $encoding;

    public function setUp(): void
    {
        self::$encoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');
    }

    public function tearDown(): void
    {
        mb_internal_encoding(self::$encoding);
    }

    public function testNullValues()
    {
        $calculator = new SizeCalculator();
        $fontSize = 12;
        self::assertLessThanOrEqual(1 + $fontSize * 0.1, $calculator->getCellWidth('Arial', $fontSize, null));
    }

    public function testGetCellWidthForUnknownFont()
    {
        $calculator = new SizeCalculator();
        $fontName = 'IAmAnUnknownFont';
        $string = 'Abcd 328 - 123 XY!!!';

        $expectedValues = array(8 => 16, 9 => 18, 12 => 24, 13 => 26);
        foreach ($expectedValues as $fontSize => $minWidth) {
            self::assertGreaterThanOrEqual($minWidth, $calculator->getCellWidth($fontName, $fontSize, $string));
            self::assertLessThanOrEqual($minWidth + 2, $calculator->getCellWidth($fontName, $fontSize, $string));
        }
    }

    public function testGetCellWidthForKnownFontReturnsLargerValueForLargerFontSize()
    {
        $calculator = new SizeCalculator($this->getFontsDirectory());
        $availableFonts = array_keys($calculator->getFonts());

        if (0 === count($availableFonts)) {
            self::markTestSkipped('No open/true type fonts found - skipping');
        }

        $fontName = array_pop($availableFonts);
        $string = 'äßö22 4eä18 åæçè €äÜ';

        $cellWidth1 = $calculator->getCellWidth($fontName, 12, $string);
        $cellWidth2 = $calculator->getCellWidth($fontName, 16, $string);

        self::assertGreaterThan($cellWidth1, $cellWidth2);
    }

    private function getFontsDirectory()
    {
        return false !== stripos(php_uname('s'), 'win')
            ? 'C:/Windows/Fonts/'
            : '/usr/share/fonts';
    }
}
