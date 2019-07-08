<?php

namespace OneSheetTest;

use OneSheet\Workbook;
use OneSheet\Xml\WorkbookXml;

class WorkbookTest extends \PHPUnit_Framework_TestCase
{
    public function testWorkbookWithPrintTitles()
    {
        $workbook = new Workbook();
        $workbook->setPrintTitleRange(1, 1);
        $expectedXml = sprintf(
            WorkbookXml::WORKBOOK_XML, sprintf(WorkbookXml::DEFINED_NAMES_XML, 1, 1)
        );
        $this->assertEquals($workbook->getWorkbookXml(), $expectedXml);
    }

    public function testWorkbookWithoutPrintTitles()
    {
        $workbook = new Workbook();
        $expectedXml = sprintf(WorkbookXml::WORKBOOK_XML, '');
        $this->assertEquals($workbook->getWorkbookXml(), $expectedXml);
    }
}