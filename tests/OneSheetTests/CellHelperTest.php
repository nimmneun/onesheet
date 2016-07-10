<?php

namespace OneSheetTests;

use OneSheet\CellHelper;

class TestCellHelper extends \PHPUnit_Framework_TestCase
{
    public function testBuildString()
    {
        $string = CellHelper::buildCell(33, 44, "987456");
        $this->assertEquals('<c r="AS33" s="0"><v>987456</v></c>', $string);

        $string = CellHelper::buildCell(77, 2222, "some text", 2);
        $this->assertEquals('<c r="CGM77" s="2" t="inlineStr"><is><t>some text</t></is></c>', $string);

        $string = CellHelper::buildCell(1, 0, "this & that", 1);
        $this->assertEquals('<c r="A1" s="1" t="inlineStr"><is><t>this &amp; that</t></is></c>', $string);
    }
}
