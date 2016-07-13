<?php

namespace OneSheetTests;

use OneSheet\Sheet;
use OneSheet\StyleHelper;

class SheetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sheet
     */
    protected static $sheet;

    /**
     * @var string
     */
    private static $path;

    public static function setUpBeforeClass()
    {
        self::$sheet = Sheet::fromDefaults();
        self::$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sheet1.xml';
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('OneSheet\Sheet', Sheet::fromDefaults('A2'));
    }

    public function testSheetFilePath()
    {
        $this->assertEquals(
            self::$path, self::$sheet->sheetFilePath()
        );
    }

    public function testAddStyle()
    {
        $this->assertGreaterThan(1, StyleHelper::buildStyle($this->getMockBuilder('\\OneSheet\\Style')->getMock()));
    }

    public function testAddRows()
    {
        $number = time();
        $string = uniqid();

        self::$sheet->addRows(array(array($number)), 1);
        $style = $this->getMockBuilder('\\OneSheet\\Style')->getMock();
        self::$sheet->addRows(array(array($string)), $style);
        $xml = file_get_contents(self::$path);

        $this->assertEquals(1, preg_match('~'. $number . '.*' . $string . '~', $xml, $match));
    }
}
