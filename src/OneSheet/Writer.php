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
     * Initialize writer defaults.
     */
    private function initialize()
    {
        $this->sheetFile = new SheetFile();
        $this->sheetFile->fwrite(str_repeat(' ', pow(2, 20)) . '<sheetData>');
        $this->sheet = new Sheet();
        $this->styler = new Styler();
    }

    /**
     * @return Sheet
     */
    public function getSheet()
    {
        return $this->sheet;
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
        $style = $this->loadOrRegisterStyle($style);
        $this->sheetFile->fwrite($this->sheet->addRow($row, $style));
    }

    /**
     * Load or register a given style.
     *
     * @param Style $style
     * @return Style
     */
    private function loadOrRegisterStyle(Style $style = null)
    {
        if ($style instanceof Style) {
            $this->styler->addStyle($style);
        } else {
            $style = $this->styler->getStyleById(0);
        }

        return $style;
    }

    /**
     * Wrap things up and write/output xlsx.
     *
     * @param string $fileName
     */
    public function writeToFile($fileName = 'dummy.xlsx')
    {
        $finalizer = new Finalizer($this->sheet, $this->styler, $this->sheetFile);
        $finalizer->finalize($fileName);
    }
}
