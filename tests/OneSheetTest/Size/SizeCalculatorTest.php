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

    public function testGetCellWidth()
    {
        $fontName = 'Calibri';
        $calculator = new SizeCalculator(null);
        $string = 'Abcd 328 - 123 XY!!';

        $expectedValues = array(8 => 12.5, 11 => 16.5, 12 => 18.5, 13 => 20.5);
        foreach ($expectedValues as $fontSize => $minWidth) {
            $this->assertGreaterThanOrEqual($minWidth, $calculator->getCellWidth($fontName, $fontSize, $string));
            $this->assertLessThanOrEqual($minWidth + 2, $calculator->getCellWidth($fontName, $fontSize, $string));
        }
    }

    public function testGetCellWidthMultiByte()
    {
        $fontName = 'Segoe UI';
        $calculator = new SizeCalculator(null);
        $string = 'ä ö22 4eä18 åæçè €äÜuköß ÄöÜÖö üfzp!';

        $expectedValues = array(8 => 30, 11 => 40, 12 => 42, 13 => 46);
        foreach ($expectedValues as $fontSize => $minWidth) {
            $this->assertGreaterThanOrEqual($minWidth, $calculator->getCellWidth($fontName, $fontSize, $string));
            $this->assertLessThanOrEqual($minWidth + 2, $calculator->getCellWidth($fontName, $fontSize, $string));
        }
    }
}
