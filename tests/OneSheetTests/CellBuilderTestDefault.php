<?php

namespace OneSheetTests;

use OneSheet\DefaultCellBuilder;

class TestDefaultCellBuilder extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $cellBuilder = new DefaultCellBuilder();

        $string = $cellBuilder->build(33, 44, 987456);
        $this->assertEquals('<c r="AS33" s="0"><v>987456</v></c>', $string);

        $string = $cellBuilder->build(33, 44, "987456");
        $this->assertEquals('<c r="AS33" s="0" t="inlineStr"><is><t>987456</t></is></c>', $string);

        $string = $cellBuilder->build(33, 44, 987.456);
        $this->assertEquals('<c r="AS33" s="0"><v>987.456</v></c>', $string);

        $string = $cellBuilder->build(33, 44, "987.456");
        $this->assertEquals('<c r="AS33" s="0" t="inlineStr"><is><t>987.456</t></is></c>', $string);

        $string = $cellBuilder->build(77, 2222, "some text", 2);
        $this->assertEquals('<c r="CGM77" s="2" t="inlineStr"><is><t>some text</t></is></c>', $string);

        $string = $cellBuilder->build(1, 0, "this & that", 1);
        $this->assertEquals('<c r="A1" s="1" t="inlineStr"><is><t>this &amp; that</t></is></c>', $string);

        $string = $cellBuilder->build(1, 0, chr(11), 1);
        $this->assertEquals('<c r="A1" s="1" t="inlineStr"><is><t>_x000B_</t></is></c>', $string);
    }
}
