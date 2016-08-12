<?php

namespace OneSheet;

use OneSheet\Style\Styler;
use OneSheet\Xml\DefaultXml;
use OneSheet\Xml\SheetXml;

/**
 * Class Finalizer, a rather quick and dirty solution for now.
 *
 * @package OneSheet
 */
class Finalizer
{
    /**
     * @var Sheet
     */
    private $sheet;

    /**
     * @var SheetFile
     */
    private $sheetFile;

    /**
     * @var \ZipArchive
     */
    private $zip;

    /**
     * @var Styler
     */
    private $styler;

    /**
     * Finalizer constructor.
     *
     * @param Sheet       $sheet
     * @param Styler      $styler
     * @param SheetFile   $sheetFile
     */
    public function __construct(Sheet $sheet, Styler $styler, SheetFile $sheetFile)
    {
        $this->sheet = $sheet;
        $this->styler = $styler;
        $this->sheetFile = $sheetFile;
        $this->zip = new \ZipArchive();

    }

    /**
     * Finalize the xlsx file.
     *
     * @param string $fileName
     */
    public function finalize($fileName)
    {
        $this->zip->open($fileName, \ZipArchive::CREATE);
        $this->finalizeSheet();
        $this->finalizeStyles();
        $this->writeDefaultXmls();
        $this->zip->close();
    }

    /**
     * Wrap up the sheet (write header, column xmls).
     */
    private function finalizeSheet()
    {
        $this->sheetFile->fwrite('</sheetData></worksheet>');
        $this->sheetFile->rewind();
        $this->sheetFile->fwrite(SheetXml::HEADER_XML);
        $this->sheetFile->fwrite(sprintf(SheetXml::DIMENSION_XML, $this->sheet->getDimensionUpperBound()));
        $this->sheetFile->fwrite(sprintf(SheetXml::SHEETVIEWS_XML, '1', 'A2')); // freeze
        $this->writeColumnWidths();
        $this->zip->addFile($this->sheetFile->getFilePath(), 'xl/worksheets/sheet1.xml');
    }

    /**
     * Write column widths xml string.
     */
    private function writeColumnWidths()
    {
        if (0 < count($this->sheet->getCellWidths())) {
            $this->sheetFile->fwrite('<cols>');
            foreach ($this->sheet->getCellWidths() as $columnNumber => $columnWidth) {
                $this->sheetFile->fwrite(sprintf(SheetXml::COLUMN_XML, ($columnNumber + 1), ($columnNumber + 1),
                    $columnWidth));
            }
            $this->sheetFile->fwrite('</cols>');
        }
    }

    /**
     * Write style xml file.
     */
    private function finalizeStyles()
    {
        $this->zip->addFromString('xl/styles.xml', $this->styler->getStyleSheetXml());
    }

    /**
     * Add default xmls to zip archive.
     */
    private function writeDefaultXmls()
    {
        $this->zip->addFromString('[Content_Types].xml', DefaultXml::CONTENT_TYPES);
        $this->zip->addFromString('docProps/core.xml',
            sprintf(DefaultXml::DOCPROPS_CORE, date(DATE_ISO8601), date(DATE_ISO8601)));
        $this->zip->addFromString('docProps/app.xml', DefaultXml::DOCPROPS_APP);
        $this->zip->addFromString('_rels/.rels', DefaultXml::RELS_RELS);
        $this->zip->addFromString('xl/_rels/workbook.xml.rels', DefaultXml::XL_RELS_WORKBOOK);
        $this->zip->addFromString('xl/workbook.xml', DefaultXml::XL_WORKBOOK);
    }
}
