<?php
/**
 * @author neun
 * @since  2016-08-12
 */

namespace OneSheetTest;

use OneSheet\Sheet;
use OneSheet\Style\Style;

class SheetTest extends \PHPUnit_Framework_TestCase
{
    public function testEnableCellWidthEstimation()
    {
        $sheet = new Sheet();
        $sheet->enableCellWidthEstimation();
        $this->assertTrue($sheet->isCellWidthEstimationEnabled());
    }

    public function testDisableCellWidthEstimation()
    {
        $sheet = new Sheet();
        $sheet->disableCellWidthEstimation();
        $this->assertFalse($sheet->isCellWidthEstimationEnabled());
    }

    public function testAddRow()
    {
        $sheet = new Sheet();
        $sheet->enableCellWidthEstimation();
        $expectedXml = '<row r="1" spans="1:2"><c r="A1" s="0" t="inlineStr"><is><t>Heinz</t></is></c><c r="B1" s="0" t="inlineStr"><is><t>Becker</t></is></c></row>';
        $xml = $sheet->addRow(array('Heinz', 'Becker'), new Style());
        $this->assertEquals($expectedXml, $xml);
    }
}
