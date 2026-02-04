<?php

namespace OneSheet;

use InvalidArgumentException;
use OneSheet\Size\SizeCalculator;
use OneSheet\Style\Font;
use OneSheet\Style\Style;
use OneSheet\Xml\RowXml;
use OneSheet\Xml\SheetXml;

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
     * @var string
     */
    private $freezePaneCellId = '';

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

    public function __construct(CellBuilder $cellBuilder, SizeCalculator $sizeCalculator)
    {
        $this->cellBuilder = $cellBuilder;
        $this->sizeCalculator = $sizeCalculator;
    }

    /**
     * Enable cell auto-sizing (~30-100% performance hit!).
     */
    public function enableCellAutosizing(): void
    {
        $this->useCellAutosizing = true;
    }

    /**
     * Disable cell auto-sizing (default).
     */
    public function disableCellAutosizing(): void
    {
        $this->useCellAutosizing = false;
    }

    /**
     * @param string $cellId
     */
    public function setFreezePaneCellId($cellId): void
    {
        $this->freezePaneCellId = $cellId;
    }

    /**
     * Set custom column widths with 0 representing the first column.
     *
     * @param int[]|float[] $columnWidths
     *
     * @throws InvalidArgumentException
     */
    public function setFixedColumnWidths(array $columnWidths): void
    {
        if ($columnWidths !== array_filter($columnWidths, 'is_numeric')
            || array_keys($columnWidths) !== array_filter(array_keys($columnWidths), 'is_int')
        ) {
            throw new InvalidArgumentException('Array must contain integer keys and numeric values only!');
        }

        $this->columnWidths = $columnWidths + $this->columnWidths;
    }

    /**
     * Set lower and/or upper limits for column widths.
     *
     * @param int|float|null $minWidth
     * @param int|float|null $maxWidth
     */
    public function setColumnWidthLimits($minWidth = null, $maxWidth = null): void
    {
        $this->minColumnWidth = is_numeric($minWidth) && $minWidth >= 0 ? $minWidth : 0;
        $this->maxColumnWidth = is_numeric($maxWidth) && $maxWidth < 255.86 ? $maxWidth : 255.86;
    }

    /**
     * Return array containing all column widths, limited to min or max
     * column width, if one or both of them are set.
     *
     * @return int[]|float[]
     */
    public function getColumnWidths(): array
    {
        foreach ($this->columnWidths as $column => $width) {
            if ($width > $this->maxColumnWidth) {
                $this->columnWidths[$column] = $this->maxColumnWidth;
            } elseif ($width < $this->minColumnWidth) {
                $this->columnWidths[$column] = $this->minColumnWidth;
            }
        }

        return $this->columnWidths;
    }

    /**
     * Add single row and style to sheet.
     *
     * @param array $row
     * @param Style $style
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function addRow(array $row, Style $style): string
    {
        $columnCount = count($row);
        $this->updateMaxColumnCount($columnCount);
        $cellXml = $this->getCellXml($row, $style);

        return $this->getRowXml($columnCount, $cellXml);
    }

    /**
     * Track column count for dimensions xml (e.g. A1:K123).
     *
     * @param int $columnCount
     */
    private function updateMaxColumnCount($columnCount): void
    {
        if ($this->maxColumnCount < $columnCount) {
            $this->maxColumnCount = $columnCount;
        }
    }

    /**
     * Build and return xml string for single row.
     *
     * @param int    $columnCount
     * @param string $cellXml
     */
    private function getRowXml($columnCount, $cellXml): string
    {
        return sprintf(RowXml::DEFAULT_XML, $this->rowIndex++, $columnCount, $cellXml);
    }

    /**
     * Build and return xml string for single cell and update cell widths.
     *
     * @param array $row
     * @param Style $style
     *
     * @return string
     * @throws InvalidArgumentException
     */
    private function getCellXml(array $row, Style $style): string
    {
        $cellXml = '';
        foreach (array_values($row) as $cellIndex => $cellValue) {
            $this->updateColumnWidths($cellValue, $cellIndex, $style->getFont());
            $cellXml .= $this->cellBuilder->build(
                $this->rowIndex, $cellIndex, $cellValue, $style->getId()
            );
        }

        return $cellXml;
    }

    /**
     * Track cell width for column width sizing if its enabled.
     *
     * @param mixed $value
     * @param int   $cellIndex
     * @param Font  $font
     */
    private function updateColumnWidths($value, $cellIndex, Font $font): void
    {
        if ($this->useCellAutosizing) {
            $cellWidth = $this->sizeCalculator->getCellWidth($font->getName(), $font->getSize(), $value);
            if (!isset($this->columnWidths[$cellIndex])
                || $this->columnWidths[$cellIndex] < $cellWidth
            ) {
                $this->columnWidths[$cellIndex] = $cellWidth;
            }
        }
    }

    /**
     * Return <dimension> xml string.
     */
    public function getDimensionXml(): string
    {
        return sprintf(SheetXml::DIMENSION_XML,
            $this->cellBuilder->getCellId($this->maxColumnCount - 1, $this->rowIndex - 1)
        );
    }

    /**
     * Return <sheetViews> xml containing the freeze pane.
     */
    public function getSheetViewsXml(): string
    {
        if (1 !== preg_match('~^[A-Z]+(\d+)$~', $this->freezePaneCellId, $m)) {
            return '';
        }

        return sprintf(SheetXml::SHEETVIEWS_XML, array_pop($m) - 1, $this->freezePaneCellId);
    }

    /**
     * Return <cols> xml for column widths or an empty string,
     * if there are no column widths.
     * Format widths to account for locales with comma as decimal point.
     */
    public function getColsXml(): string
    {
        $colsXml = '';
        if (0 !== count($this->getColumnWidths())) {
            foreach ($this->getColumnWidths() as $columnIndex => $columnWidth) {
                $columnNumber = $columnIndex + 1;
                $colsXml .= sprintf(
                    SheetXml::COLUMN_XML, $columnNumber, $columnNumber, number_format($columnWidth, 3, '.', '')
                );
            }
            $colsXml = sprintf('<cols>%s</cols>', $colsXml);
        }

        return $colsXml;
    }

    /**
     * Return array of available font names and paths.
     */
    public function getFonts(): array
    {
        return $this->sizeCalculator->getFonts();
    }
}
