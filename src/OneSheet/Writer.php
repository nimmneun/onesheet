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
        $this->sheetFile = new SheetFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid() . '.xml', 'wb+');
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
     * @param array     $rows
     * @param Style|int $style
     */
    public function addRows(array $rows, $style = 0)
    {
        foreach ($rows as $row) {
            $this->addRow($row, $style);
        }
    }

    /**
     * @param array     $row
     * @param Style|int $style
     */
    public function addRow(array $row, $style = 0)
    {
        $style = $this->loadOrRegisterStyle($style);
        $this->sheetFile->fwrite($this->sheet->addRow($row, $style));
    }

    /**
     * Load or register a given style.
     *
     * @param Style|int $style
     * @return Style
     */
    private function loadOrRegisterStyle($style = 0)
    {
        if ($style instanceof Style) {
            $this->styler->addStyle($style);
        } else {
            $style = $this->styler->getStyleById((int)$style);
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
