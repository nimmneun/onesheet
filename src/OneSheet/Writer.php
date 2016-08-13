<?php

namespace OneSheet;

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
     * @var string
     */
    private $encoding;

    /**
     * Writer constructor.
     *
     * @param string $encoding
     */
    public function __construct($encoding = 'utf-8')
    {
        $this->encoding = $encoding;
        $this->initialize();
    }

    /**
     * All cells _above_ this cell will be fozen/fixed.
     *
     * @param int $cellId
     */
    public function setfreezePaneCellId($cellId)
    {
        $this->sheet->setFreezePaneId($cellId);
    }

    /**
     *  @return Writer
     */
    public function enableCellAutosizing()
    {
        $this->sheet->enableCellAutosizing();
        return $this;
    }

    /**
     * Initialize writer defaults.
     */
    private function initialize()
    {
        $this->sheetFile = new SheetFile();
        $this->sheetFile->fwrite(str_repeat(' ', pow(2, 20)) . '<sheetData>');
        $this->styler = new Styler();
        $this->sheet = new Sheet();
    }

    /**
     * @param array $rows
     * @param Style $style
     */
    public function addRows(array $rows, Style $style = null)
    {
        if (count($rows) !== count($rows, COUNT_RECURSIVE)) {
            foreach ($rows as $row) {
                $this->addRow($row, $style);
            }
        }
    }

    /**
     * @param array $row
     * @param Style $style
     */
    public function addRow(array $row, Style $style = null)
    {
        $style = $style instanceof Style ? $style : $this->styler->getDefaultStyle();
        $this->styler->addStyle($style);
        $this->sheetFile->fwrite($this->sheet->addRow($row, $style));
    }

    /**
     * Wrap things up and write/output xlsx.
     *
     * @param string $fileName
     */
    public function writeToFile($fileName = 'dummy.xlsx')
    {
        $finalizer = new Finalizer($this->sheet, $this->styler, $this->sheetFile, $this->encoding);
        $finalizer->finalize($fileName);
    }
}
