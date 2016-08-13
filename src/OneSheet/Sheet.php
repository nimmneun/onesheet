<?php

namespace OneSheet;

use OneSheet\Style\Style;
use OneSheet\Width\WidthCalculator;
use OneSheet\Xml\RowXml;

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
     * @var WidthCalculator
     */
    private $widthCalculator;

    /**
     * @var bool
     */
    private $useCellAutosizing = false;

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
    private $maxRowWidth;

    /**
     * Holds widths of the widest cells for column sizing.
     *
     * @var array
     */
    private $columnWidths;

    /**
     * Sheet constructor.
     */
    public function __construct()
    {
        $this->cellBuilder = new CellBuilder();
        $this->widthCalculator = new WidthCalculator();
    }

    /**
     * Enable cell autosizing (~30% performance hit!).
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
     * @return bool
     */
    public function isCellAutosizingEnabled()
    {
        return $this->useCellAutosizing;
    }

    /**
     * Return array containing all column widths.
     *
     * @return array
     */
    public function getColumnWidths()
    {
        return $this->columnWidths;
    }

    /**
     * Return cellId for dimensions.
     *
     * @return string
     */
    public function getDimensionUpperBound()
    {
        return $this->cellBuilder->getCellId($this->maxRowWidth, $this->rowIndex - 1);
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
        $rowWidth = count($row);
        $this->updateMaxRowWidth($rowWidth);

        $this->widthCalculator->setFont($style->font());
        $cellXml = $this->getCellXml($row, $style);

        if (!$this->useCellAutosizing || $style->font()->getSize() < 14) {
            return sprintf(RowXml::DEFAULT_XML, $this->rowIndex++, $rowWidth, $cellXml);
        }

        return sprintf(RowXml::HEIGHT_XML, $this->rowIndex++, $rowWidth,
            $style->font()->getSize() * 1.4, $cellXml);
    }

    /**
     * Track widest row for dimensions xml (e.g. A1:K123).
     *
     * @param int $rowWidth
     */
    private function updateMaxRowWidth($rowWidth)
    {
        if ($this->maxRowWidth < $rowWidth) {
            $this->maxRowWidth = $rowWidth;
        }
    }

    /**
     * Get xml string for single cell and update cell widths.
     *
     * @param array $row
     * @param Style $style
     * @return string
     */
    private function getCellXml(array $row, Style $style)
    {
        $cellXml = '';
        foreach (array_values($row) as $cellIndex => $cellValue) {
            if (0 == strlen($cellValue)) {
                continue;
            }
            if ($this->useCellAutosizing) {
                $this->updateColumnWidths($cellValue, $cellIndex, $style);
            }
            $cellXml .= $this->cellBuilder->build($this->rowIndex, $cellIndex, $cellValue,
                $style->getId());
        }

        return $cellXml;
    }

    /**
     * Track cell width for column width sizing.
     *
     * @param mixed $value
     * @param int   $cellIndex
     * @param Style $style
     */
    private function updateColumnWidths($value, $cellIndex, Style $style)
    {
        $cellWidth = $this->widthCalculator->getCellWidth($value, $style->font()->isBold());

        if (!isset($this->columnWidths[$cellIndex])
            || $this->columnWidths[$cellIndex] < $cellWidth
        ) {
            $this->columnWidths[$cellIndex] = $cellWidth;
        }
    }
}
