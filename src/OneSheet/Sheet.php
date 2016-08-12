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
     * Track next row index.
     *
     * @var int
     */
    private $rowIndex = 1;

    /**
     * @var bool
     */
    private $useCellWidthEstimation = false;

    /**
     * Hold width of the widest row.
     *
     * @var int
     */
    private $maxRowWidth;

    /**
     * Holds widths of the widest cells for column sizing.
     *
     * @var array
     */
    private $cellWidths;

    /**
     * Sheet constructor.
     */
    public function __construct()
    {
        $this->cellBuilder = new CellBuilder();
        $this->widthCalculator = new WidthCalculator();
    }

    /**
     * Enable cell width estimation.
     */
    public function enableCellWidthEstimation()
    {
        $this->useCellWidthEstimation = true;
    }

    /**
     * Disable cell width estimation (default).
     */
    public function disableCellWidthEstimation()
    {
        $this->useCellWidthEstimation = false;
    }

    /**
     * @return boolean
     */
    public function isCellWidthEstimationEnabled()
    {
        return $this->useCellWidthEstimation;
    }

    /**
     * Return array containing all cell widths.
     *
     * @return array
     */
    public function getCellWidths()
    {
        return $this->cellWidths;
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
     * @return string
     */
    public function addRow(array $row, Style $style)
    {
        $rowWidth = count($row);
        $this->updateMaxRowWidth($rowWidth);

        $this->widthCalculator->setFont($style->font());
        $cellXml = $this->getCellXml($row, $style);

        if (13 > $style->font()->getSize()) {
            return sprintf(RowXml::DEFAULT_XML, $this->rowIndex++, $rowWidth, $cellXml);
        }
        return sprintf(RowXml::HEIGHT_XML, $this->rowIndex++, $rowWidth, $style->font()->getSize() * 1.4, $cellXml);
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
            if ($this->useCellWidthEstimation) {
                $this->updateCellWidths($cellValue, $cellIndex, $style);
            }
            $cellXml .= $this->cellBuilder->build($this->rowIndex, $cellIndex, $cellValue, $style->getId());
        }

        return $cellXml;
    }

    /**
     * Track cell width for column width sizing.
     *
     * @param mixed $value
     * @param       $cellIndex
     * @param Style $style
     */
    private function updateCellWidths($value, $cellIndex, Style $style)
    {
        $cellWidth = $this->widthCalculator->getCellWidth($value, $style->font()->isBold());

        if (!isset($this->cellWidths[$cellIndex])
            || $this->cellWidths[$cellIndex] < $cellWidth
        ) {
            $this->cellWidths[$cellIndex] = $cellWidth;
        }
    }
}
