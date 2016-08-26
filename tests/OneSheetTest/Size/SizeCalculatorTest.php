<?php

namespace OneSheetTest\Size;

use OneSheet\Size\SizeCalculator;
use OneSheet\Size\SizeCollection;
use OneSheet\Style\Font;

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
        $font = new Font();
        $font->setName('Calibri');
        $calculator = new SizeCalculator(new SizeCollection());
        $string = 'Abcd 328 - 123 XY!!';

        $expectedValues = array(8 => 12, 11 => 17, 12 => 19, 13 => 20);
        foreach ($expectedValues as $fontSize => $minWidth) {
            $calculator->setFont($font->setSize($fontSize));

            $this->assertGreaterThanOrEqual($minWidth, $calculator->getCellWidth($string, $font));
            $this->assertLessThanOrEqual($minWidth + 2, $calculator->getCellWidth($string, $font));
        }
    }

    public function testGetCellWidthMultiByte()
    {

        $font = new Font();
        $font->setName('Segoe UI');
        $calculator = new SizeCalculator(new SizeCollection());
        $string = 'ä ö32 4eä18 åæçè €äÜuköß ÄöÜÖö üfzp!';

        $expectedValues = array(8 => 29, 11 => 39, 12 => 42.5, 13 => 46);
        foreach ($expectedValues as $fontSize => $minWidth) {
            $calculator->setFont($font->setSize($fontSize));

            $this->assertGreaterThanOrEqual($minWidth, $calculator->getCellWidth($string, $font));
            $this->assertLessThanOrEqual($minWidth + 2, $calculator->getCellWidth($string, $font));
        }
    }
}
