<?php

namespace OneSheet;

use OneSheet\Xml\RowXml;
use OneSheet\Style\Style;
use OneSheet\Xml\SheetXml;
use OneSheet\Size\SizeCalculator;

/**
 * Class Sheet
 *
 * @package OneSheet
 */
class Sheet
{
    /**
     * @var CellBuilder
     */
    private $cellBuilder;

    /**
     * @var SizeCalculator
     */
    private $sizeCalculator;

    /**
     * @var bool
     */
    private $useCellAutosizing = false;

    /**
     * @var int
     */
    private $freezePaneCellId;

    /**
     * Track next row index.
     *
     * @var int
     */
    private $rowIndex = 1;

    /**
     * Holds width/column count of the widest row.
     *
     * @var int
     */
    private $maxColumnCount;

    /**
     * Holds widths of the widest cells for column sizing.
     *
     * @var array
     */
    private $columnWidths = array();

    /**
     * Holds minimum allowed column width.
     *
     * @var float|int
     */
    private $minColumnWidth = 0;

    /**
     * Holds maximum allowed column width. 254.86 appears
     * to be the default maximum width.
     *
     * @var float|int
     */
    private $maxColumnWidth = 254.86;

    /**
     * Sheet constructor.
     *
     * @param CellBuilder    $cellBuilder
     * @param SizeCalculator $sizeCalculator
     */
    public function __construct(CellBuilder $cellBuilder, SizeCalculator $sizeCalculator)
    {
        $this->cellBuilder = $cellBuilder;
        $this->sizeCalculator = $sizeCalculator;
    }

    /**
     * Enable cell autosizing (~30-100% performance hit!).
     */
    public function enableCellAutosizing()
    {
        $this->useCellAutosizing = true;
    }

    /**
     * Disable cell autosizing (default).
     */
    public function disableCellAutosizing()
    {
        $this->useCellAutosizing = false;
    }

    /**
     * @param int $cellId
     */
    public function setFreezePaneCellId($cellId)
    {
        $this->freezePaneCellId = $cellId;
    }

    /**
     * Set custom column widths with 0 representing the first column.
     *
     * @param array $columnWidths
     * @throws \InvalidArgumentException
     */
    public function setFixedColumnWidths(array $columnWidths)
    {
        if ($columnWidths !== array_filter($columnWidths, 'is_numeric')
            || array_keys($columnWidths) !== array_filter(array_keys($columnWidths), 'is_int')
        ) {
            throw new \InvalidArgumentException('Array must contain integer keys and numeric values only!');
        }

        $this->columnWidths = $columnWidths + $this->columnWidths;
    }

    /**
     * Set lower and/or upper limits for column widths.
     *
     * @param float|null $minWidth
     * @param float|null $maxWidth
     */
    public function setColumnWidthLimits($minWidth = null, $maxWidth = null)
    {
        $this->minColumnWidth = is_numeric($minWidth) && $minWidth >= 0 ? $minWidth : 0;
        $this->maxColumnWidth = is_numeric($maxWidth) && $maxWidth < 255.86 ? $maxWidth : 255.86;
    }

    /**
     * Return array containing all column widths, limited to min or max
     * column width, if one or both of them are set.
     *
     * @return array
     */
    public function getColumnWidths()
    {
        foreach ($this->columnWidths as $column => $width) {
            if ($this->maxColumnWidth && $width > $this->maxColumnWidth) {
                $this->columnWidths[$column] = $this->maxColumnWidth;
            } elseif ($this->minColumnWidth && $width < $this->minColumnWidth) {
                $this->columnWidths[$column] = $this->minColumnWidth;
            }
        }

        return $this->columnWidths;
    }

    /**
     * Return cellId for dimensions.
     *
     * @return string
     */
    public function getDimensionUpperBound()
    {
        return $this->cellBuilder->getCellId($this->maxColumnCount, $this->rowIndex - 1);
    }

    /**
     * Add single row and style to sheet.
     *
     * @param array $row
     * @param Style $style
     *
     * @return string
     */
    public function addRow(array $row, Style $style)
    {
        $columnCount = count($row);
        $this->updateMaxColumnCount($columnCount);

        $this->sizeCalculator->setFont($style->getFont());
        $cellXml = $this->getCellXml($row, $style);

        return $this->getRowXml($style, $columnCount, $cellXml);
    }

    /**
     * Track column count for dimensions xml (e.g. A1:K123).
     *
     * @param int $columnCount
     */
    private function updateMaxColumnCount($columnCount)
    {
        if ($this->maxColumnCount < $columnCount) {
            $this->maxColumnCount = $columnCount;
        }
    }

    /**
     * Build and return xml string for single row.
     *
     * @param Style  $style
     * @param int    $columnCount
     * @param string $cellXml
     * @return string
     */
    private function getRowXml(Style $style, $columnCount, $cellXml)
    {
        if ($style->getFont()->getSize() < 14) {
            return sprintf(RowXml::DEFAULT_XML, $this->rowIndex++, $columnCount, $cellXml);
        }

        return sprintf(RowXml::HEIGHT_XML, $this->rowIndex++, $columnCount,
            $this->sizeCalculator->getRowHeight(), $cellXml);
    }

    /**
     * Build and return xml string for single cell and update cell widths.
     *
     * @param array $row
     * @param Style $style
     * @return string
     */
    private function getCellXml(array $row, Style $style)
    {
        $cellXml = '';
        foreach (array_values($row) as $cellIndex => $cellValue) {
            if (0 < strlen($cellValue)) {
                $this->updateColumnWidths($cellValue, $cellIndex, $style);
                $cellXml .= $this->cellBuilder->build(
                    $this->rowIndex, $cellIndex, $cellValue, $style->getId()
                );
            }
        }

        return $cellXml;
    }

    /**
     * Track cell width for column width sizing if its enabled.
     *
     * @param mixed $value
     * @param int   $cellIndex
     * @param Style $style
     */
    private function updateColumnWidths($value, $cellIndex, Style $style)
    {
        if ($this->useCellAutosizing) {
            $cellWidth = $this->sizeCalculator->getCellWidth($value, $style->getFont());
            if (!isset($this->columnWidths[$cellIndex])
                || $this->columnWidths[$cellIndex] < $cellWidth
            ) {
                $this->columnWidths[$cellIndex] = $cellWidth;
            }
        }
    }

    /**
     * Return freeze pane xml string for sheetView.
     *
     * @return string
     */
    public function getFreezePaneXml()
    {
        if (!$this->freezePaneCellId
            || 1 !== preg_match('~^[A-Z]+(\d+)$~', $this->freezePaneCellId, $m)
        ) {
            return '';
        }
        return sprintf(SheetXml::FREEZE_PANE_XML, array_pop($m) - 1, $this->freezePaneCellId);
    }
}
