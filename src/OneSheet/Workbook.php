<?php

namespace OneSheet;

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
     * @return string
     */
    public function getWorkbookXml()
    {
        $definedNames = '';
        if ($this->printTitleStart && $this->printTitleEnd) {
            $definedNames = sprintf(
                WorkbookXml::DEFINED_NAMES_XML,
                $this->printTitleStart,
                $this->printTitleEnd
            );
        }

        return sprintf(WorkbookXml::WORKBOOK_XML, $definedNames);
    }
}