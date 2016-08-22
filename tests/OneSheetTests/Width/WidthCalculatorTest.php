<?php

namespace OneSheetTest\Width;

use OneSheet\Style\Font;
use OneSheet\Width\WidthCalculator;
use OneSheet\Width\WidthCollection;

class WidthCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCellWidth()
    {
        $font = new Font();
        $font->setName('Calibri')->setSize(14);
        $calculator = new WidthCalculator(new WidthCollection());
        $calculator->setFont($font);

        $string = 'Abcd 328 - 123 XY!!';
        $this->assertGreaterThanOrEqual(23, $calculator->getCellWidth($string, $font));
        $this->assertLessThanOrEqual(25, $calculator->getCellWidth($string, $font));
    }

    public function testGetCellWidthMultiByte()
    {
        $font = new Font();
        $font->setName('Segoe UI')->setSize(12);
        $calculator = new WidthCalculator(new WidthCollection());
        $calculator->setFont($font);

        $string = 'ä ö32 4eä18 4eä €äÜuköß ÄöÜÖö üfzp!';
        $this->assertGreaterThanOrEqual(42, $calculator->getCellWidth($string, $font));
        $this->assertLessThanOrEqual(45, $calculator->getCellWidth($string, $font));
    }
}
