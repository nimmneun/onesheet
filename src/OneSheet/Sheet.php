<?php
/**
 * @author neun
 * @since  2016-07-03
 */

namespace OneSheet;

/**
 * Class Sheet to write the sheet1.xml contents to file.
 * @package OneSheet
 */
class Sheet
{
    /**
     * Contains the <sheetData> section.
     *
     * @var \SplFileObject
     */
    private $spl;

    /**
     * @var RowBuilderInterface
     */
    private $rowBuilder;

    /**
     * Sheet constructor to init SplFileObject and write xml header.
     * Optionally supply a cell id like e.g. A2 to add freeze pane.
     * It has to be done right away, since everything is immediately
     * written to the SplFileObject.
     *
     * @param RowBuilderInterface $rowBuilder
     * @param string|null         $freezePaneCellId
     */
    public function __construct(RowBuilderInterface $rowBuilder, $freezePaneCellId = null)
    {
        $this->rowBuilder = $rowBuilder;

        $this->spl = new \SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sheet1.xml', 'wb+');
        $this->spl->fwrite(SheetXml::HEADER_XML);

        if (1 == preg_match('~[A-Z]+([1-9]?[0-9]+)$~', $freezePaneCellId, $match)) {
            $this->spl->fwrite(sprintf(SheetXml::SHEETVIEWS_XML, (array_pop($match) - 1), $freezePaneCellId));
        }

        $this->spl->fwrite('<sheetData>');
    }

    /**
     * Instantiate new sheet with default RowBuilder implementation.
     *
     * @param string|null $freezePaneCellId
     * @return Sheet
     */
    public static function fromDefaults($freezePaneCellId = null)
    {
        return new self(new DefaultRowBuilder(new DefaultCellBuilder()), $freezePaneCellId);
    }

    /**
     * Add single data row to sheet and add new style,
     * if its not an integer.
     *
     * @param array     $dataRow
     * @param int|Style $style
     */
    public function addRow(array $dataRow, $style = 0)
    {
        if (!is_int($style)) {
            $style = $this->addStyle($style);
        }

        $this->spl->fwrite($this->rowBuilder->build($dataRow, $style));
    }

    /**
     * Add multiple data rows to sheet.
     *
     * @param array     $dataRows
     * @param int|Style $style
     */
    public function addRows(array $dataRows, $style = 0)
    {
        foreach ($dataRows as $dataRow) {
            $this->addRow($dataRow, $style);
        }
    }

    /**
     * Add new style and return its id.
     *
     * @param Style $style
     * @return int
     */
    public function addStyle(Style $style)
    {
        return StyleHelper::buildStyle($style);
    }

    /**
     * Write closing xml tag and return path to sheet file.
     *
     * @return string
     */
    public function sheetFilePath()
    {
        $this->spl->fwrite('</sheetData></worksheet>');
        return (string)$this->spl->getFileInfo();
    }
}
