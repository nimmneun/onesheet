<?php

namespace OneSheetTest;

use OneSheet\Workbook;
use OneSheet\Xml\WorkbookXml;

class WorkbookTest extends \PHPUnit_Framework_TestCase
{
    const SHEET_ID = 'Sheet1';

    public function testWorkbookWithPrintTitles()
    {
        $workbook = new Workbook();
        $workbook->setPrintTitleRange(1, 1);

        $expectedXml = sprintf(
            WorkbookXml::WORKBOOK_XML,
            sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, self::SHEET_ID, 1, 1),
            sprintf(WorkbookXml::DEFINED_NAMES_XML, self::SHEET_ID, 1, 1)
        );
        $this->assertEquals($workbook->getWorkbookXml([self::SHEET_ID]), $expectedXml);
    }

    public function testWorkbookWithoutPrintTitles()
    {
        $workbook = new Workbook();
        $expectedXml = sprintf(
            WorkbookXml::WORKBOOK_XML,
            sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, self::SHEET_ID, 1, 1),
            ''
        );
        $this->assertEquals($workbook->getWorkbookXml([self::SHEET_ID]), $expectedXml);
    }
}
