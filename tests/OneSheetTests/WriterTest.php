<?php
/**
 * @author neun
 * @since  2016-07-10
 */

namespace OneSheetTests;

use OneSheet\Sheet;
use OneSheet\Writer;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSheet()
    {
        $sheet = new Sheet();
        $writer = new Writer($sheet);
        $this->assertEquals($writer->sheet(), $sheet);
    }

    public function testClose()
    {
        $writer = new Writer(new Sheet(), sys_get_temp_dir() . '/onesheet.xlsx');
        $writer->close();
        $this->assertFileExists(sys_get_temp_dir() . '/onesheet.xlsx');
    }
}
