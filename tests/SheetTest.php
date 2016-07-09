<?php

class SheetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OneSheet\Sheet
     */
    protected static $sheet;

    /**
     * @var string
     */
    private static $path;

    public static function setUpBeforeClass()
    {
        self::$sheet = new \OneSheet\Sheet('A2');
        self::$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sheet1.xml';
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('OneSheet\Sheet', new OneSheet\Sheet('B2'));
    }

    public function testSheetFilePath()
    {
        $this->assertEquals(
            self::$path, self::$sheet->sheetFilePath()
        );
    }

    public function testAddStyle()
    {
        $this->assertGreaterThan(1, \OneSheet\StyleHelper::buildStyle(new OneSheet\Style()));
    }

    public function testAddRows()
    {
        $number = time();
        $string = uniqid();

        self::$sheet->addRows(array(array($number)), 1);
        $style = new \OneSheet\Style();
        self::$sheet->addRows(array(array($string)), $style);
        $xml = file_get_contents(self::$path);

        $this->assertEquals(1, preg_match('~'. $number . '.*' . $string . '~', $xml, $match));
    }



}
