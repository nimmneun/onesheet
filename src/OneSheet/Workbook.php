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
     * @param string $sheetName
     */
    public function setPrintTitleRange($startRow, $endRow, $sheetName)
    {
        if ($startRow <= $endRow && is_numeric($startRow) && is_numeric($endRow)) {
            $this->printTitleStarts[$sheetName] = (int)$startRow;
            $this->printTitleEnds[$sheetName] = (int)$endRow;
        }
    }

    /**
     * @param string[] $sheetNames
     * @return string
     */
    public function getWorkbookXml(array $sheetNames)
    {
        $sheets = $this->getSheetsXml($sheetNames);
        $definedNames = $this->getDefinedNamesXml($sheetNames);

        return sprintf(WorkbookXml::WORKBOOK_XML, $sheets, $definedNames);
    }

    /**
     * @param string[] $sheetNames
     * @return string
     */
    public function getWorkbookRelsXml($sheetNames)
    {
        $sheets = '';
        foreach ($sheetNames as $key => $sheetName) {
            $sheets .= sprintf(WorkbookXml::WORKBOOK_REL_XML, $key + 1, $sheetName);
        }

        return sprintf(WorkbookXml::WORKBOOK_RELS_XML, $sheets);
    }

    /**
     * @param string[] $sheetNames
     * @return string
     */
    private function getSheetsXml($sheetNames)
    {
        $sheets = '';
        foreach ($sheetNames as $key => $sheetName) {
            $sheets .= sprintf(WorkbookXml::WORKBOOK_SHEETS_XML, $sheetName, $key + 1, $key + 1);
        }

        return $sheets;
    }

    /**
     * @param string[] $sheetNames
     * @return string
     */
    private function getDefinedNamesXml(array $sheetNames)
    {
        $definedNames = '';
        if ($this->printTitleStarts && $this->printTitleEnds) {
            $definedNameTags = '';
            foreach ($sheetNames as $key => $sheetName) {
                $definedNameTags .= $this->createSingleDefinedNameXml($sheetName, $key);
            }
            $definedNames = sprintf(WorkbookXml::DEFINED_NAMES_XML, $definedNameTags);
        }

        return $definedNames;
    }

    /**
     * $localSheetId is the only one that has to start from 0 instead of 1.
     *
     * @param string $sheetName
     * @param int    $localSheetId
     * @return string
     */
    private function createSingleDefinedNameXml($sheetName, $localSheetId)
    {
        return isset($this->printTitleEnds[$sheetName])
            ? sprintf(
                WorkbookXml::DEFINED_NAME_XML,
                $localSheetId,
                $sheetName,
                $this->printTitleStarts[$sheetName],
                $this->printTitleEnds[$sheetName])
            : '';
    }
}
