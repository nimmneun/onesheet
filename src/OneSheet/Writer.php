<?php

namespace OneSheet;

use OneSheet\Size\SizeCalculator;
use OneSheet\Size\SizeCollection;
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
     * @var
     */
    private $output;

    /**
     * Writer constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * All cells _above_ this cell will be frozen/fixed.
     *
     * @param int $cellId
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
     * Initialize writer defaults.
     */
    private function initialize()
    {
        $this->sheetFile = new SheetFile();
        $this->sheetFile->fwrite(str_repeat(' ', 1024 * 1024) . '<sheetData>');
        $this->styler = new Styler();
        $this->sheet = new Sheet(new CellBuilder(), new SizeCalculator(new SizeCollection()));
    }

    /**
     * @param array $rows
     * @param Style $style
     * @throws \InvalidArgumentException
     */
    public function addRows(array $rows, Style $style = null)
    {
        if (count($rows) === count($rows, COUNT_RECURSIVE)) {
            throw new \InvalidArgumentException('Array must contain arrays!');
        }

        foreach ($rows as $row) {
            $this->addRow($row, $style);
        }
    }

    /**
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
     * Output headers & content and unlink the xlsx file eventually.
     *
     * @param string $fileName
     */
    private function sendHeaders($fileName)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
    }
}
