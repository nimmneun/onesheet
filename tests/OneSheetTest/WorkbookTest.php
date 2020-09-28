<?php

namespace OneSheetTest;

use OneSheet\Workbook;
use OneSheet\Xml\WorkbookXml;

class WorkbookTest extends \PHPUnit_Framework_TestCase
{
    const SHEET_ONE = 'Sheet1';
    const SHEET_TWO = 'Sheet2';

    public function testWorkbookWithPrintTitles()
    {
        $workbook = new Workbook();
        $workbook->setPrintTitleRange(1, 1, self::SHEET_ONE);

        $expectedXml = sprintf(
            WorkbookXml::WORKBOOK_XML,
            sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, self::SHEET_ONE, 1, 1),
            sprintf(WorkbookXml::DEFINED_NAMES_XML,
                sprintf(WorkbookXml::DEFINED_NAME_XML, 0, self::SHEET_ONE, 1, 1)
            )
        );
        $this->assertEquals($workbook->getWorkbookXml([self::SHEET_ONE]), $expectedXml);
    }

    public function testWorkBookWithMultiplePrintTitles()
    {
        $workbook = new Workbook();
        $workbook->setPrintTitleRange(1, 1, self::SHEET_ONE);
        $workbook->setPrintTitleRange(1, 1, self::SHEET_TWO);

        $expectedXml = sprintf(
            WorkbookXml::WORKBOOK_XML,
            sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, self::SHEET_ONE, 1, 1) .
            sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, self::SHEET_TWO, 2, 2),
            sprintf(WorkbookXml::DEFINED_NAMES_XML,
                sprintf(WorkbookXml::DEFINED_NAME_XML, 0, self::SHEET_ONE, 1, 1) .
                sprintf(WorkbookXml::DEFINED_NAME_XML, 1, self::SHEET_TWO, 1, 1)
            )
        );
        $this->assertEquals($workbook->getWorkbookXml([self::SHEET_ONE, self::SHEET_TWO]), $expectedXml);
    }

    public function testWorkbookWithoutPrintTitles()
    {
        $workbook = new Workbook();
        $expectedXml = sprintf(
            WorkbookXml::WORKBOOK_XML,
            sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, self::SHEET_ONE, 1, 1),
            ''
        );
        $this->assertEquals($workbook->getWorkbookXml([self::SHEET_ONE]), $expectedXml);
    }
}
