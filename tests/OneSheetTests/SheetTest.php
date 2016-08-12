<?php

namespace OneSheetTest;

use OneSheet\Sheet;
use OneSheet\Style\Style;

class SheetTest extends \PHPUnit_Framework_TestCase
{
    public function testEnableCellAutosizing()
    {
        $sheet = new Sheet();
        $sheet->enableCellAutosizing();
        $this->assertTrue($sheet->isCellAutosizingEnabled());
    }

    public function testDisableCellAutosizing()
    {
        $sheet = new Sheet();
        $sheet->disableCellAutosizing();
        $this->assertFalse($sheet->isCellAutosizingEnabled());
    }

    public function testAddRow()
    {
        $sheet = new Sheet();
        $sheet->enableCellAutosizing();
        $expectedXml = '<row r="1" spans="1:3"><c r="A1" s="0" t="inlineStr"><is><t>Heinz</t></is></c><c r="B1" s="0" t="inlineStr"><is><t>Becker &amp; Decker</t></is></c><c r="C1" s="0" t="b"><v>1</v></c></row>';
        $xml = $sheet->addRow(array('Heinz', 'Becker & Decker', true), new Style());
        $this->assertEquals($expectedXml, $xml);
    }
}
