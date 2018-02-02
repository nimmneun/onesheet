<?php

namespace OneSheet;

use OneSheet\Size\SizeCalculator;
use OneSheet\Style\Style;
use OneSheet\Style\Styler;

/**
 * Class Writer
 *
 * @package OneSheet
 */
class Writer
{
    /**
     * @var SheetFile
     */
    private $sheetFile;

    /**
     * @var Styler
     */
    private $styler;

    /**
     * @var Sheet
     */
    private $sheet;

    /**
     * @var resource
     */
    private $output;

    /**
     * If a $fontsDirectory is supplied it will be scanned for usable ttf/otf fonts
     * to be used for cell auto-sizing. Keep in mind though - viewers of an excel
     * file have to have that font on their machine. XLSX does not embed fonts!
     *
     * @param string|null $fontsDirectory
     * @throws \Exception
     */
    public function __construct($fontsDirectory = null)
    {
        $this->sheetFile = new SheetFile();
        $this->sheetFile->fwrite(str_repeat(' ', 1024 * 1024) . '<sheetData>');
        $this->styler = new Styler();
        $this->sheet = new Sheet(new CellBuilder(), new SizeCalculator($fontsDirectory));
    }

    /**
     * All cells _above_ this cell will be frozen/fixed.
     *
     * @param string $cellId
     */
    public function setFreezePaneCellId($cellId)
    {
        $this->sheet->setFreezePaneCellId($cellId);
    }

    /**
     * Set fixed column widths per cell (no ranges) and array index
     * 0 being the first column.
     * If used alongside cell autosizing, these should be set
     * after the last row has been added.
     *
     * @param array $columnWidths
     * @throws \InvalidArgumentException
     */
    public function setFixedColumnWidths(array $columnWidths)
    {
        $this->sheet->setFixedColumnWidths($columnWidths);
    }

    /**
     * Set lower and/or upper limits for column widths.
     *
     * @param float|null $minWidth
     * @param float|null $maxWidth
     */
    public function setColumnWidthLimits($minWidth = null, $maxWidth = null)
    {
        $this->sheet->setColumnWidthLimits($minWidth, $maxWidth);
    }

    /**
     * Start recording row specs for column autosizing.
     *
     * @return Writer
     */
    public function enableCellAutosizing()
    {
        $this->sheet->enableCellAutosizing();
        return $this;
    }

    /**
     * Stop recording row specs for column autosizing.
     *
     * @return Writer
     */
    public function disableCellAutosizing()
    {
        $this->sheet->disableCellAutosizing();
        return $this;
    }

    /**
     * Add multiple rows at once.
     *
     * @param array|\Traversable $rows
     * @param Style $style
     * @throws \InvalidArgumentException
     */
    public function addRows($rows, Style $style = null)
    {
        if (!(is_array($rows) || is_object($rows) && $rows instanceof \Traversable)) {
            throw new \InvalidArgumentException('Expected array or traversable object as rows', 1517564833);
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
    public function addRow(array $row, Style $style = null)
    {
        if (!empty($row)) {
            $style = $style instanceof Style ? $style : $this->styler->getDefaultStyle();
            $this->styler->addStyle($style);
            $this->sheetFile->fwrite($this->sheet->addRow($row, $style));
        }
    }

    /**
     * Wrap things up and write xlsx.
     *
     * @param string $fileName
     */
    public function writeToFile($fileName = 'report.xlsx')
    {
        $this->output = fopen($fileName, 'w');
        $finalizer = new Finalizer($this->sheet, $this->styler, $this->sheetFile);
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
        $finalizer = new Finalizer($this->sheet, $this->styler, $this->sheetFile);
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
     * Return array of available fonts & paths as key value pairs.
     *
     * @return array
     */
    public function getFonts()
    {
        return $this->sheet->getFonts();
    }
}
