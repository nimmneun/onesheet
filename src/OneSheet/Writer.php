<?php

namespace OneSheet;

use OneSheet\Size\SizeCalculator;
use OneSheet\Style\Style;
use OneSheet\Style\Styler;

class Writer
{
    /**
     * @var SheetFile[]
     */
    private $sheetFiles;

    /**
     * @var Styler
     */
    private $styler;

    /**
     * @var Sheet[]
     */
    private $sheets;

    /**
     * @var Workbook
     */
    private $workbook;

    /**
     * @var resource
     */
    private $output;

    /**
     * @var string
     */
    private $currentSheet;

    /**
     * If a $fontsDirectory is supplied it will be scanned for usable ttf/otf fonts
     * to be used for cell auto-sizing. Keep in mind though - viewers of an excel
     * file have to have that font on their machine. XLSX does not embed fonts!
     *
     * @param string|null $fontsDirectory
     * @param string|int  $sheetName
     * @throws \Exception
     */
    public function __construct($fontsDirectory = null, $sheetName = 'sheet1')
    {
        $this->styler = new Styler();
        $this->switchSheet($sheetName, $fontsDirectory);
        $this->workbook = new Workbook();
    }

    /**
     * All cells _above_ this cell (e.g. A2) will be frozen/fixed.
     *
     * @param string $cellId
     */
    public function setFreezePaneCellId($cellId)
    {
        $this->sheets[$this->currentSheet]->setFreezePaneCellId($cellId);
    }

    /**
     * Set the range of rows to repeat at the top of each page when printing the
     * excel file. $startRow=1 and $endRow=1 will repeat the first row only.
     *
     * @param int $startRow
     * @param int $endRow
     */
    public function setPrintTitleRange($startRow, $endRow)
    {
        $this->workbook->setPrintTitleRange($startRow, $endRow, $this->currentSheet);
    }

    /**
     * Set fixed column widths per cell (no ranges) and array index
     * 0 being the first column.
     * If used alongside cell autosizing, these should be set
     * after the last row has been added.
     *
     * @param int[]|float[] $columnWidths
     * @throws \InvalidArgumentException
     */
    public function setFixedColumnWidths(array $columnWidths)
    {
        $this->sheets[$this->currentSheet]->setFixedColumnWidths($columnWidths);
    }

    /**
     * Set lower and/or upper limits for column widths.
     *
     * @param int|float|null $minWidth
     * @param int|float|null $maxWidth
     */
    public function setColumnWidthLimits($minWidth = null, $maxWidth = null)
    {
        $this->sheets[$this->currentSheet]->setColumnWidthLimits($minWidth, $maxWidth);
    }

    /**
     * Start recording row specs for column auto-sizing.
     *
     * @return Writer
     */
    public function enableCellAutosizing()
    {
        $this->sheets[$this->currentSheet]->enableCellAutosizing();
        return $this;
    }

    /**
     * Stop recording row specs for column auto-sizing.
     *
     * @return Writer
     */
    public function disableCellAutosizing()
    {
        $this->sheets[$this->currentSheet]->disableCellAutosizing();
        return $this;
    }

    /**
     * Add multiple rows at once.
     *
     * @param array|\Traversable $rows
     * @param Style              $style
     * @throws \InvalidArgumentException
     */
    public function addRows($rows, Style $style = null)
    {
        if (!is_array($rows) && false === $rows instanceof \Traversable) {
            throw new \InvalidArgumentException('Expected array or traversable object as rows');
        }

        foreach ($rows as $row) {
            $this->addRow($row, $style);
        }
    }

    /**
     * Add a single new row to the sheet and supply an optional style.
     *
     * @param array $row
     * @param Style $style
     */
    public function addRow(array $row, $styles)
    {
        if (!empty($row)) {
            foreach ($styles as $style) {
                $newStyle = $style instanceof Style ? $style : $this->styler->getDefaultStyle();
                $this->styler->addStyle($newStyle);
            }

            $this->sheetFiles[$this->currentSheet]->fwrite(
                $this->sheets[$this->currentSheet]->addRow($row, $styles)
            );
        }
    }

    /**
     * @param string|int  $sheetName
     * @param string|null $fontsDirectory
     * @throws \Exception
     */
    public function switchSheet($sheetName, $fontsDirectory = null)
    {
        $sheetName = is_int($sheetName) ? sprintf('sheet%s', $sheetName) : $sheetName;
        isset($this->sheets[$sheetName]) || $this->createNewSheet($fontsDirectory, $sheetName);
        $this->currentSheet = $sheetName;
    }

    /**
     * Wrap things up and write xlsx.
     *
     * @param string $fileName
     */
    public function writeToFile($fileName = 'report.xlsx')
    {
        $this->output = fopen($fileName, 'w');
        $finalizer = new Finalizer($this->sheets, $this->styler, $this->sheetFiles, $this->workbook);
        $finalizer->finalize($this->output);
    }

    /**
     * Wrap things up and send xlsx to browser.
     *
     * @param string $fileName
     */
    public function writeToBrowser($fileName = 'report.xlsx')
    {
        $this->output = fopen('php://output', 'w');
        $finalizer = new Finalizer($this->sheets, $this->styler, $this->sheetFiles, $this->workbook);
        $this->sendHeaders($fileName);
        $finalizer->finalize($this->output);
    }

    /**
     * Send headers for browser output.
     *
     * @param string $fileName
     */
    private function sendHeaders($fileName)
    {
        if (!headers_sent()) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
        }
    }

    /**
     * @param string $fontsDirectory
     * @param string $sheetName
     * @throws \Exception|\InvalidArgumentException
     */
    private function createNewSheet($fontsDirectory, $sheetName)
    {
        $pattern = '\/*?:\[\]';
        if (strlen($sheetName) === 0 || 1 === preg_match('~[' . $pattern . ']~', $sheetName)) {
            throw new \InvalidArgumentException(
                sprintf('sheet name must not be empty and not contain %s', $pattern)
            );
        }
        $this->sheetFiles[$sheetName] = new SheetFile();
        $this->sheetFiles[$sheetName]->fwrite(str_repeat(' ', 1024 * 1024) . '<sheetData>');
        $this->sheets[$sheetName] = new Sheet(new CellBuilder(), new SizeCalculator($fontsDirectory));
    }

    /**
     * Return array of available fonts & paths as key value pairs.
     *
     * @return array
     */
    public function getFonts()
    {
        return $this->sheets[$this->currentSheet]->getFonts();
    }
}
