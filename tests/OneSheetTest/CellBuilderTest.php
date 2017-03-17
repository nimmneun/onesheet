<?php

namespace OneSheetTest;

use OneSheet\CellBuilder;

class CellBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCellId()
    {
        $cellBuilder = new CellBuilder();
        $this->assertEquals('A1', $cellBuilder->getCellId(0, 1));
        $this->assertEquals('Y9', $cellBuilder->getCellId(24, 9));
        $this->assertEquals('ALL9', $cellBuilder->getCellId(999, 9));
    }

    public function testBuildContentCell()
    {
        $cellBuilder = new CellBuilder();

        $expectedXml = '<c r="D1" s="0" t="inlineStr"><is><t>Something</t></is></c>';
        $this->assertEquals($expectedXml, $cellBuilder->build(1, 3, 'Something'));

        $expectedXml = '<c r="D1" s="0" t="inlineStr"><is><t>_x0003_</t></is></c>';
        $this->assertEquals($expectedXml, $cellBuilder->build(1, 3, chr(3)));
    }

    public function testBuildEmptyCell()
    {
        $cellBuilder = new CellBuilder();

        $expectedXml = '';
        $this->assertEquals($expectedXml, $cellBuilder->build(1, 3, ''));

        $expectedXml = '<c r="D1" s="1"/>';
        $this->assertEquals($expectedXml, $cellBuilder->build(1, 3, '', 1));
    }
}
