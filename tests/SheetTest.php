<?php

class SheetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OneSheet\Sheet
     */
    protected static $sheet;

    public static function setUpBeforeClass()
    {
        self::$sheet = new \OneSheet\Sheet('A2');
    }

    public function testSheetFilePath()
    {
        $this->assertEquals(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sheet1.xml', self::$sheet->sheetFilePath());
    }

    public function testAddStyle()
    {
        $style = $this->getMock('OneSheet\\Style');
        $this->assertGreaterThan(1, \OneSheet\StyleHelper::buildStyle($style));
    }
}
