<?php

namespace OneSheet;

use OneSheet\Xml\DefaultXml;
use OneSheet\Xml\WorkbookXml;

class Workbook
{
    /**
     * @var int
     */
    private $printTitleStart;

    /**
     * @var int
     */
    private $printTitleEnd;

    /**
     * Set the range of rows to repeat when printing the excel file.
     * $startRow=1 and $endRow=1 will repeat the first row only.
     *
     * @param int $startRow
     * @param int $endRow
     */
    public function setPrintTitleRange($startRow, $endRow)
    {
        if ($startRow <= $endRow && is_numeric($startRow) && is_numeric($endRow)) {
            $this->printTitleStart = (int)$startRow;
            $this->printTitleEnd = (int)$endRow;
        }
    }

    /**
     * @param array $sheetIds
     * @return string
     */
    public function getWorkbookXml(array $sheetIds)
    {
        $sheets = $this->getSheetsXml($sheetIds);
        $definedNames = $this->getDefinedNamesXml();

        return sprintf(WorkbookXml::WORKBOOK_XML, $sheets, $definedNames);
    }

    /**
     * @param array $sheetIds
     * @return string
     */
    public function getWorkbookRelsXml($sheetIds)
    {
        $sheets = '';
        foreach ($sheetIds as $key => $sheetId) {
            $sheets .= sprintf(WorkbookXml::WORKBOOK_REL_XML, $key + 1, $sheetId);
        }

        return sprintf(WorkbookXml::WORKBOOK_RELS_XML, $sheets);
    }

    /**
     * @param array $sheetIds
     * @return string
     */
    private function getSheetsXml($sheetIds)
    {
        $sheets = '';
        foreach ($sheetIds as $key => $sheetId) {
            $sheets .= sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, $sheetId, $key + 1, $key + 1);
        }

        return $sheets;
    }

    /**
     * @return string
     */
    private function getDefinedNamesXml()
    {
        $definedNames = '';
        if ($this->printTitleStart && $this->printTitleEnd) {
            $definedNames = sprintf(
                WorkbookXml::DEFINED_NAMES_XML,
                $this->printTitleStart,
                $this->printTitleEnd
            );
        }

        return $definedNames;
    }
}
