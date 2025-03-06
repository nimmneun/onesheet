<?php

namespace OneSheetTest;

use OneSheet\CellBuilder;
use OneSheet\Sheet;
use OneSheet\Size\SizeCalculator;
use OneSheet\Style\Style;
use PHPUnit\Framework\TestCase;

class SheetTest extends TestCase
{
    public function testAddRow()
    {
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        $expectedXml = '<row r="1" spans="1:3"><c r="A1" s="0" t="inlineStr"><is><t>Heinz</t></is></c><c r="B1" s="0" t="inlineStr"><is><t>Becker &amp; Decker</t></is></c><c r="C1" s="0" t="b"><v>1</v></c></row>';
        $xml = $sheet->addRow(array('Heinz', 'Becker & Decker', true), new Style());
        self::assertEquals($expectedXml, $xml);
    }

    public function testEnableAutoSizing()
    {
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        self::assertCount(0, $sheet->getColumnWidths());

        $sheet->enableCellAutosizing();
        $sheet->addRow(array(1,2,3), new Style());
        $sheet->disableCellAutosizing();
        $sheet->addRow(array(1,2,3,4,5,6,7), new Style());
        self::assertCount(3, $sheet->getColumnWidths());
    }

    public function testGetFreezePaneXml()
    {
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        self::assertEquals('', $sheet->getSheetViewsXml());

        $sheet->setFreezePaneCellId('A5');
        $expectedXml = '<sheetViews><sheetView workbookViewId="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>';
        self::assertEquals($expectedXml, $sheet->getSheetViewsXml());
    }

    public function testWidthCalculation()
    {
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        $sheet->addRow(array(1,2,3), new Style());
        self::assertEmpty($sheet->getColumnWidths());

        $sheet->enableCellAutosizing();
        $sheet->addRow(array(1,2,3), new Style());
        self::assertNotEmpty($sheet->getColumnWidths());

        $widthSum = array_sum($sheet->getColumnWidths());
        $largerStyle = new Style();
        $largerStyle->setFontName('Something')->setFontSize(15);
        $sheet->addRow(array(1,2,3), $largerStyle);
        self::assertGreaterThan($widthSum, array_sum($sheet->getColumnWidths()));
    }

    public function testSetFixedColumnWidths()
    {
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        $sheet->setFixedColumnWidths(array(0 => 10, 3 => 20, 7 => 10));
        self::assertEquals(40, array_sum($sheet->getColumnWidths()));

        $sheet->setFixedColumnWidths(array(3 => 10));
        self::assertEquals(30, array_sum($sheet->getColumnWidths()));
    }

    public function testSetFixedColumnWidthsWithInvalidValues()
    {
        $this->expectException(\InvalidArgumentException::class);
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        $sheet->setFixedColumnWidths(array(1 => 'abc', 7 => 10));
    }

    public function testSetFixedColumnWidthsWithInvalidKeys()
    {
        $this->expectException(\InvalidArgumentException::class);
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        $sheet->setFixedColumnWidths(array('A1' => 10, 7 => 10));
    }

    public function testGetColumnWidths()
    {
        $sheet = new Sheet(new CellBuilder(), new SizeCalculator(null));
        $sheet->enableCellAutosizing();
        $sheet->setColumnWidthLimits(5, 50);
        $sheet->addRow(array('A', str_repeat('XYZ', 100)), new Style());

        self::assertEquals(55, array_sum($sheet->getColumnWidths()));
    }
}
