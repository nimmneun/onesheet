<?php

namespace OneSheetTests;

use OneSheet\RowHelper;

class RowHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildRow()
    {
        $string = '<row r="1" spans="1:3"><c r="A1" s="0" t="inlineStr"><is><t>123.321</t></is></c><c r="B1" s="0"><v>123</v></c><c r="C1" s="0" t="inlineStr"><is><t>cool &amp; quiet</t></is></c></row>';
        $data = array('123.321', 123, 'cool & quiet');
        $this->assertEquals($string, RowHelper::buildRow($data));
    }
}
