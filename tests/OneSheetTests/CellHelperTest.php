<?php

namespace OneSheetTests;

use OneSheet\CellHelper;
use OneSheet\Sheet;

class TestCellHelper extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // stupid but right now required to fill $chars in CellHelper
        new Sheet();
    }

    public function testBuildCell()
    {
        $string = CellHelper::buildCell(33, 44, 987456);
        $this->assertEquals('<c r="AS33" s="0"><v>987456</v></c>', $string);

        $string = CellHelper::buildCell(33, 44, "987456");
        $this->assertEquals('<c r="AS33" s="0" t="inlineStr"><is><t>987456</t></is></c>', $string);

        $string = CellHelper::buildCell(33, 44, 987.456);
        $this->assertEquals('<c r="AS33" s="0"><v>987.456</v></c>', $string);

        $string = CellHelper::buildCell(33, 44, "987.456");
        $this->assertEquals('<c r="AS33" s="0" t="inlineStr"><is><t>987.456</t></is></c>', $string);

        $string = CellHelper::buildCell(77, 2222, "some text", 2);
        $this->assertEquals('<c r="CGM77" s="2" t="inlineStr"><is><t>some text</t></is></c>', $string);

        $string = CellHelper::buildCell(1, 0, "this & that", 1);
        $this->assertEquals('<c r="A1" s="1" t="inlineStr"><is><t>this &amp; that</t></is></c>', $string);

        $string = CellHelper::buildCell(1, 0, chr(11), 1);
        $this->assertEquals('<c r="A1" s="1" t="inlineStr"><is><t>_x000B_</t></is></c>', $string);
    }
}
