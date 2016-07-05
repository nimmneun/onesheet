<?php
/**
 * Simple hacky xlsx writer. Since performance and memory usage were the
 * main drivers, DOM and SimpleXml where out of the question. Same goes
 * for Cell and even Row objects.
 * While it might have been nice for a sheet to hold a row collection
 * and rows to hold cell collections etc ... it was a total no-go
 * memory and performance wise.
 *
 * This lib was built to satisfy the following needs:
 * - Write a single sheet with up to 2^20 rows fast and with a small
 *   memory footprint.
 * - Freeze the first [n] rows to have a fixed table header/headline.
 * - Option to use different fonts, styles and background colors on
 *   a row level.
 *
 * Current major drawback(s):
 * - No cell individualisation, everything is applied at a row level
 *   and its intended to keep it that way.
 * - No calculated/formula cells. Only inlineStr and simple number type
 *   cells and it will probably stay that way.
 * - No control character escaping todo: RowHelper::addEscapeRow()
 *
 * @author neun
 * @since  2016-07-03
 */

namespace OneSheet;

/**
 * Class Writer
 * @package Sheety
 */
class Writer
{
    /**
     * @var \ZipArchive
     */
    private $zip;

    /**
     * @var Sheet
     */
    private $sheet;

    /**
     * XmlWriter constructor.
     * @param string $fileName
     * @param Sheet $sheet
     */
    public function __construct($fileName = 'dummy.xlsx', Sheet $sheet)
    {
        $this->zip = new \ZipArchive();
        $this->zip->open($fileName, \ZipArchive::CREATE + \ZipArchive::CM_STORE);
        $this->sheet = $sheet;
    }

    /**
     * Return sheet instance.
     *
     * @return Sheet
     */
    public function sheet()
    {
        return $this->sheet;
    }

    /**
     * Write xml style sheet into zip.
     */
    private function writeStyleXmlFile()
    {
        $this->zip->addFromString('xl/styles.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . StyleHelper::getFontsXml()
            . StyleHelper::getFillsXml()
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf borderId="0" fillId="0" fontId="0" numFmtId="0"/></cellStyleXfs>'
            . StyleHelper::getCellXfsXml()
            . '<cellStyles count="1"><cellStyle builtinId="0" name="Normal" xfId="0"/></cellStyles><tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/></styleSheet>'
        );
    }

    /**
     * Add required default xml files to zip archive.
     */
    private function writeDefaultXmls()
    {
        $this->zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default ContentType="application/xml" Extension="xml"/><Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet1.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" PartName="/xl/styles.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml" PartName="/xl/sharedStrings.xml"/><Override ContentType="application/vnd.openxmlformats-package.core-properties+xml" PartName="/docProps/core.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" PartName="/docProps/app.xml"/></Types>');
        $this->zip->addFromString('docProps/core.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dcterms:created xsi:type="dcterms:W3CDTF">' . date(DATE_ISO8601) . '</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">' . date(DATE_ISO8601) . '</dcterms:modified><cp:revision>0</cp:revision></cp:coreProperties>');
        $this->zip->addFromString('docProps/app.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>OneSheetOld</Application><TotalTime>0</TotalTime></Properties>');
        $this->zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdWorkbook" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rIdCore" Type="http://schemas.openxmlformats.org/officedocument/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rIdApp" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>');
        $this->zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdStyles" Target="styles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/><Relationship Id="rIdSharedStrings" Target="sharedStrings.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"/><Relationship Id="rIdSheet1" Target="worksheets/sheet1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/></Relationships>');
        $this->zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="0" uniqueCount="0"></sst>');
        $this->zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Sheet1" sheetId="1" r:id="rIdSheet1"/></sheets></workbook>');
    }

    /**
     * Write/add the style + sheet xml files and close/write
     * the xlsx file.
     */
    public function close()
    {
        $this->zip->addFile($this->sheet->sheetFilePath(), 'xl/worksheets/sheet1.xml');
        $this->writeStyleXmlFile();
        $this->writeDefaultXmls();
        $this->zip->close();
    }
}
