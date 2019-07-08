<?php

namespace OneSheet;

use OneSheet\Style\Styler;
use OneSheet\Xml\DefaultXml;
use OneSheet\Xml\SheetXml;

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
     * @var Workbook
     */
    private $workbook;

    /**
     * Finalizer constructor.
     *
     * @param Workbook $workbook
     * @param Sheet              $sheet
     * @param Styler             $styler
     * @param SheetFile          $sheetFile
     */
    public function __construct(Sheet $sheet, Styler $styler, SheetFile $sheetFile, Workbook $workbook)
    {
        $this->sheet = $sheet;
        $this->styler = $styler;
        $this->sheetFile = $sheetFile;
        $this->workbook = $workbook;
        $this->zip = new \ZipArchive();
    }

    /**
     * Finalize the xlsx file.
     *
     * @param resource $output
     */
    public function finalize($output)
    {
        $zipFileUrl = sys_get_temp_dir() . '/' . uniqid();

        $this->fillZipWithFileContents($zipFileUrl);
        if (!$this->zip->close()) {
            throw new \RuntimeException('Failed to close zip file!');
        }

        $this->copyToOutputAndCleanup($output, $zipFileUrl);
    }

    /**
     * Add all file and string contents to zip file.
     *
     * @param string $zipFileUrl
     */
    private function fillZipWithFileContents($zipFileUrl)
    {
        $this->zip->open($zipFileUrl, \ZipArchive::CREATE);
        $this->finalizeSheet();
        $this->finalizeStyles();
        $this->finalizeWorkbook();
        $this->finalizeDefaultXmls();
    }

    /**
     * Wrap up the sheet (write header, column xmls).
     */
    private function finalizeSheet()
    {
        $this->sheetFile->fwrite('</sheetData></worksheet>');
        $this->sheetFile->rewind();
        $this->sheetFile->fwrite(SheetXml::HEADER_XML);
        $this->sheetFile->fwrite($this->sheet->getDimensionXml());
        $this->sheetFile->fwrite($this->sheet->getSheetViewsXml());
        $this->sheetFile->fwrite($this->sheet->getColsXml());
        $this->zip->addFile($this->sheetFile->getFilePath(), 'xl/worksheets/sheet1.xml');
    }

    /**
     * Write style xml file.
     */
    private function finalizeStyles()
    {
        $this->zip->addFromString('xl/styles.xml', $this->styler->getStyleSheetXml());
    }

    /**
     * Write workbook file.
     */
    private function finalizeWorkbook()
    {
        $this->zip->addFromString('xl/workbook.xml', $this->workbook->getWorkbookXml());
    }

    /**
     * Add default xmls to zip archive.
     */
    private function finalizeDefaultXmls()
    {
        $this->zip->addFromString('[Content_Types].xml', DefaultXml::CONTENT_TYPES);
        $this->zip->addFromString('docProps/core.xml',
            sprintf(DefaultXml::DOCPROPS_CORE, date(DATE_ISO8601), date(DATE_ISO8601)));
        $this->zip->addFromString('docProps/app.xml', DefaultXml::DOCPROPS_APP);
        $this->zip->addFromString('_rels/.rels', DefaultXml::RELS_RELS);
        $this->zip->addFromString('xl/_rels/workbook.xml.rels', DefaultXml::XL_RELS_WORKBOOK);
    }

    /**
     * Write zip/xlsx contents to specified output
     * and unlink/delete files.
     *
     * @param resource $output
     * @param string   $zipFileUrl
     */
    private function copyToOutputAndCleanup($output, $zipFileUrl)
    {
        $zipFilePointer = fopen($zipFileUrl, 'r');
        if (!stream_copy_to_stream($zipFilePointer, $output)
            || !fclose($zipFilePointer)
            || !fclose($output)
            || !unlink($zipFileUrl)
        ) {
            throw new \RuntimeException("Failed to copy stream and clean up!");
        }
    }
}
