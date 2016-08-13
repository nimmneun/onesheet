<?php

namespace OneSheetTest;

use OneSheet\Style\Style;
use OneSheet\Writer;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSheet()
    {
        $writer = new Writer();
        $this->assertInstanceOf('OneSheet\Sheet', $writer->getSheet());
    }

    public function testAddRows()
    {
        $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'onesheet_test.xlsx';

        $writer = new Writer();
        $writer->getSheet()->enableCellAutosizing();
        $writer->addRows(array(range(1,3), range('a', 'z')), new Style());
        $writer->addRows(array(range(1,3), range('a', 'z')));
        $writer->writeToFile($fileName);

        $this->assertFileExists($fileName);
        unlink($fileName);
    }
}
