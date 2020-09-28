<?php

namespace OneSheetTest;

use OneSheet\Style\Style;
use OneSheet\Writer;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRowsWithStyle()
    {
        $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'onesheet_test.xlsx';

        $writer = new Writer();
        $writer->enableCellAutosizing();
        $writer->addRows(array(range(1, 3), range('a', 'z')), new Style());
        $writer->writeToFile($fileName);

        self::assertFileExists($fileName);
        unlink($fileName);
    }

    /**
     * @dataProvider rows
     */
    public function testAddRows($rows)
    {
        $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'onesheet_test.xlsx';

        $writer = new Writer();
        $writer->addRows($rows);
        $writer->writeToFile($fileName);

        self::assertFileExists($fileName);
        unlink($fileName);
    }

    /**
     * @return array
     */
    public function rows()
    {
        return [
            'array' => [
                [range(1, 3), range('a', 'z')],
            ],
            'traversable object' => [
                new \ArrayObject([range(1, 3), range('a', 'z')]),
            ],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddRowsExceptionOnNonArrayOrTraversableObject()
    {
        $writer = new Writer();
        $writer->addRows(new \stdClass());
    }

    public function testSetFreezePaneCellId()
    {
        $writer = new Writer();
        $writer->setFreezePaneCellId('A5');

        $sheet = $this->getFirstSheet($writer);
        $value = $this->getObjectProperty($sheet, 'freezePaneCellId');

        self::assertEquals('A5', $value);
    }

    public function testEnableCellAutosizing()
    {
        $writer = new Writer();
        $writer->enableCellAutosizing();

        $sheet = $this->getFirstSheet($writer);
        $value = $this->getObjectProperty($sheet, 'useCellAutosizing');

        self::assertEquals(true, $value);
    }

    public function testDisableCellAutosizing()
    {
        $writer = new Writer();
        $writer->enableCellAutosizing();
        $writer->disableCellAutosizing();

        $sheet = $this->getFirstSheet($writer);
        $value = $this->getObjectProperty($sheet, 'useCellAutosizing');

        self::assertEquals(false, $value);
    }

    public function testSetFixedColumnWidths()
    {
        $widths = array(9, 10, 11);

        $writer = new Writer();
        $writer->setFixedColumnWidths($widths);

        $sheet = $this->getFirstSheet($writer);
        $value = $this->getObjectProperty($sheet, 'columnWidths');

        self::assertEquals($widths, $value);
        self::assertEquals(30, array_sum($value));
    }

    public function testSetColumnWidth()
    {
        $writer = new Writer();
        $writer->setColumnWidthLimits(3, 10);

        $sheet = $this->getFirstSheet($writer);
        $minWidth = $this->getObjectProperty($sheet, 'minColumnWidth');
        $maxWidth = $this->getObjectProperty($sheet, 'maxColumnWidth');

        self::assertEquals(3, $minWidth);
        self::assertEquals(10, $maxWidth);

        $writer->setColumnWidthLimits(-5, 300);
        $minWidth = $this->getObjectProperty($sheet, 'minColumnWidth');
        $maxWidth = $this->getObjectProperty($sheet, 'maxColumnWidth');
        self::assertEquals(0, $minWidth);
        self::assertEquals(255.86, $maxWidth);
    }

    public function testGetFonts()
    {
        $writer = new Writer();
        self::assertTrue(is_array($writer->getFonts()));
    }

    public function testWriteToBrowser()
    {
        $writer = new Writer();
        $writer->addRow([123,123]);

        ob_start();
        $writer->writeToBrowser();
        $output = ob_get_clean();

        self::assertGreaterThan(100, strlen($output));
    }

    private function getFirstSheet($writer)
    {
        return $this->getObjectProperty($writer, 'sheets')['Sheet1'];
    }

    private function getObjectProperty($object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
