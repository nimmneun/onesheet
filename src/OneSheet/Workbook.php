<?php

namespace OneSheet;

use OneSheet\Xml\WorkbookXml;

class Workbook
{
    /**
     * @var int[]
     */
    private $printTitleStarts;

    /**
     * @var int[]
     */
    private $printTitleEnds;

    /**
     * Set the range of rows to repeat when printing the excel file.
     * $startRow=1 and $endRow=1 will repeat the first row only.
     *
     * @param int    $startRow
     * @param int    $endRow
     * @param string $sheetId
     */
    public function setPrintTitleRange($startRow, $endRow, $sheetId)
    {
        if ($startRow <= $endRow && is_numeric($startRow) && is_numeric($endRow)) {
            $this->printTitleStarts[$sheetId] = (int)$startRow;
            $this->printTitleEnds[$sheetId] = (int)$endRow;
        }
    }

    /**
     * @param string[] $sheetIds
     * @return string
     */
    public function getWorkbookXml(array $sheetIds)
    {
        $sheets = $this->getSheetsXml($sheetIds);
        $definedNames = $this->getDefinedNamesXml($sheetIds);

        return sprintf(WorkbookXml::WORKBOOK_XML, $sheets, $definedNames);
    }

    /**
     * @param string[] $sheetIds
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
     * @param string[] $sheetIds
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
     * @param string[] $sheetIds
     * @return string
     */
    private function getDefinedNamesXml(array $sheetIds)
    {
        $definedNames = '';
        if ($this->printTitleStarts && $this->printTitleEnds) {
            foreach ($sheetIds as $key => $sheetId) {
                if (isset($this->printTitleEnds[$sheetId])) {
                    $definedNames .= sprintf(
                        WorkbookXml::DEFINED_NAMES_XML,
                        $key + 1,
                        $sheetId,
                        $this->printTitleStarts[$sheetId],
                        $this->printTitleEnds[$sheetId]
                    );
                }
            }
        }

        return $definedNames;
    }
}
