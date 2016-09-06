<?php

namespace OneSheetTest;

use OneSheet\Style\Style;
use OneSheet\Writer;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRows()
    {
        $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'onesheet_test.xlsx';

        $writer = new Writer();
        $writer->enableCellAutosizing();
        $writer->addRows(array(range(1, 3), range('a', 'z')), new Style());
        $writer->addRows(array(range(1, 3), range('a', 'z')));
        $writer->writeToFile($fileName);

        $this->assertFileExists($fileName);
        unlink($fileName);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddRowsException()
    {
        $writer = new Writer();
        $writer->addRows(array(1, 2, 3, 4, 5));
    }

    public function testSetFreezePaneCellId()
    {
        $writer = new Writer();
        $writer->setFreezePaneCellId('A5');

        $sheet = $this->getObjectProperty($writer, 'sheet');
        $value = $this->getObjectProperty($sheet, 'freezePaneCellId');

        $this->assertEquals('A5', $value);
    }

    public function testEnableCellAutosizing()
    {
        $writer = new Writer();
        $writer->enableCellAutosizing();

        $sheet = $this->getObjectProperty($writer, 'sheet');
        $value = $this->getObjectProperty($sheet, 'useCellAutosizing');

        $this->assertEquals(true, $value);
    }

    public function testDisableCellAutosizing()
    {
        $writer = new Writer();
        $writer->enableCellAutosizing();
        $writer->disableCellAutosizing();

        $sheet = $this->getObjectProperty($writer, 'sheet');
        $value = $this->getObjectProperty($sheet, 'useCellAutosizing');

        $this->assertEquals(false, $value);
    }

    public function testSetFixedColumnWidths()
    {
        $widths = array(9, 10, 11);

        $writer = new Writer();
        $writer->setFixedColumnWidths($widths);

        $sheet = $this->getObjectProperty($writer, 'sheet');
        $value = $this->getObjectProperty($sheet, 'columnWidths');

        $this->assertEquals($widths, $value);
        $this->assertEquals(30, array_sum($value));
    }

    public function testSetColumnWidth()
    {
        $writer = new Writer();
        $writer->setColumnWidthLimits(3, 10);

        $sheet = $this->getObjectProperty($writer, 'sheet');
        $minWidth = $this->getObjectProperty($sheet, 'minColumnWidth');
        $maxWidth = $this->getObjectProperty($sheet, 'maxColumnWidth');

        $this->assertEquals(3, $minWidth);
        $this->assertEquals(10, $maxWidth);

        $writer->setColumnWidthLimits(-5, 300);
        $minWidth = $this->getObjectProperty($sheet, 'minColumnWidth');
        $maxWidth = $this->getObjectProperty($sheet, 'maxColumnWidth');
        $this->assertEquals(0, $minWidth);
        $this->assertEquals(255.86, $maxWidth);
    }

    private function getObjectProperty($object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
