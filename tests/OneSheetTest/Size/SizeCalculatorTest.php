<?php

namespace OneSheetTest\Size;

use OneSheet\Size\SizeCalculator;

class SizeCalculatorTest extends \PHPUnit_Framework_TestCase
{
    private static $encoding;

    public function setUp()
    {
        self::$encoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');
    }

    public function tearDown()
    {
        mb_internal_encoding(self::$encoding);
    }

    public function testGetCellWidthForUnknownFont()
    {
        $calculator = new SizeCalculator(null);
        $fontName = 'IAmAnUnknownFont';
        $string = 'Abcd 328 - 123 XY!!!';

        $expectedValues = array(8 => 16, 9 => 18, 12 => 24, 13 => 26);
        foreach ($expectedValues as $fontSize => $minWidth) {
            $this->assertGreaterThanOrEqual($minWidth, $calculator->getCellWidth($fontName, $fontSize, $string));
            $this->assertLessThanOrEqual($minWidth + 2, $calculator->getCellWidth($fontName, $fontSize, $string));
        }
    }

    public function testGetCellWidthForKnownFontReturnsLargerValueForLargerFontSize()
    {
        $calculator = new SizeCalculator(null);
        $availableFonts = array_keys($calculator->getFonts());

        if (0 === count($availableFonts)) {
            self::markTestSkipped('No open/true type fonts found - skipping');
        }

        $fontName = array_pop($availableFonts);
        $string = 'äßö22 4eä18 åæçè €äÜ';

        $cellWidth1 = $calculator->getCellWidth($fontName, 12, $string);
        $cellWidth2 = $calculator->getCellWidth($fontName, 16, $string);

        $this->assertGreaterThan($cellWidth1, $cellWidth2);
    }
}
