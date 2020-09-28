<?php

namespace OneSheet;

use OneSheet\Style\Styler;
use OneSheet\Xml\DefaultXml;
use OneSheet\Xml\SheetXml;

class Finalizer
{
    /**
     * @var Sheet[]
     */
    private $sheets;

    /**
     * @var SheetFile[]
     */
    private $sheetFiles;

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
     * @param Workbook    $workbook
     * @param Sheet[]     $sheets
     * @param Styler      $styler
     * @param SheetFile[] $sheetFiles
     */
    public function __construct(array $sheets, Styler $styler, array $sheetFiles, Workbook $workbook)
    {
        $this->sheets = $sheets;
        $this->styler = $styler;
        $this->sheetFiles = $sheetFiles;
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
        foreach ($this->sheets as $sheetId => $sheet) {
            $this->finalizeSheet($this->sheetFiles[$sheetId], $sheet, $sheetId);
        }
        $this->finalizeWorkbook(array_keys($this->sheets));
        $this->finalizeStyles();
        $this->finalizeDefaultXmls();
    }

    /**
     * @param SheetFile $sheetFile
     * @param Sheet     $sheet
     * @param string    $sheetId
     */
    private function finalizeSheet($sheetFile, $sheet, $sheetId)
    {
        $sheetFile->fwrite('</sheetData></worksheet>');
        $sheetFile->rewind();
        $sheetFile->fwrite(SheetXml::HEADER_XML);
        $sheetFile->fwrite($sheet->getDimensionXml());
        $sheetFile->fwrite($sheet->getSheetViewsXml());
        $sheetFile->fwrite($sheet->getColsXml());
        $this->zip->addFile($sheetFile->getFilePath(), "xl/worksheets/{$sheetId}.xml");
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
     * @param array $sheetIds
     */
    private function finalizeWorkbook(array $sheetIds)
    {
        $this->zip->addFromString('xl/workbook.xml', $this->workbook->getWorkbookXml($sheetIds));
        $this->zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbook->getWorkbookRelsXml($sheetIds));
    }

    /**
     * Add default xmls to zip archive.
     */
    private function finalizeDefaultXmls()
    {
        $this->zip->addFromString('[Content_Types].xml', DefaultXml::CONTENT_TYPES);
        $this->zip->addFromString('docProps/core.xml',
            sprintf(DefaultXml::DOCPROPS_CORE, date(DATE_W3C), date(DATE_W3C)));
        $this->zip->addFromString('docProps/app.xml', DefaultXml::DOCPROPS_APP);
        $this->zip->addFromString('_rels/.rels', DefaultXml::RELS_RELS);
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
