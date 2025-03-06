<?php

namespace OneSheetTest;

use OneSheet\CellBuilder;
use PHPUnit\Framework\TestCase;

class CellBuilderTest extends TestCase
{
    public function testGetCellId()
    {
        $cellBuilder = new CellBuilder();
        self::assertEquals('A1', $cellBuilder->getCellId(0, 1));
        self::assertEquals('Y9', $cellBuilder->getCellId(24, 9));
        self::assertEquals('ALL9', $cellBuilder->getCellId(999, 9));
    }

    public function testBuildContentCell()
    {
        $cellBuilder = new CellBuilder();

        $expectedXml = '<c r="D1" s="0" t="inlineStr"><is><t>Something</t></is></c>';
        self::assertEquals($expectedXml, $cellBuilder->build(1, 3, 'Something'));

        $expectedXml = '<c r="D1" s="0" t="inlineStr"><is><t>_x0003_</t></is></c>';
        self::assertEquals($expectedXml, $cellBuilder->build(1, 3, chr(3)));
    }

    public function testBuildEmptyCell()
    {
        $cellBuilder = new CellBuilder();

        $expectedXml = '';
        self::assertEquals($expectedXml, $cellBuilder->build(1, 3, ''));

        $expectedXml = '<c r="D1" s="1"/>';
        self::assertEquals($expectedXml, $cellBuilder->build(1, 3, '', 1));
    }
}
