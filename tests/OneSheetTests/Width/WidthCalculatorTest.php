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

        $string = 'Abcd 328 - 123 Äöü';
        $this->assertGreaterThanOrEqual(23, $calculator->getCellWidth($string, $font));
        $this->assertLessThanOrEqual(25, $calculator->getCellWidth($string, $font));
    }

    public function testGetCellWidthForNonAsciiString()
    {
        $font = new Font();
        $font->setName('Segoe UI')->setSize(12);
        $calculator = new WidthCalculator(new WidthCollection());
        $calculator->setFont($font);

        $string = 'ナ ニヌ 123 ネノ абвг де ёж з';
        $this->assertGreaterThanOrEqual(31, $calculator->getCellWidth($string, $font));
        $this->assertLessThanOrEqual(34, $calculator->getCellWidth($string, $font));
    }

}
