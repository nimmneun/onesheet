<?php
/**
 * @author neun
 * @since  2016-07-03
 */

namespace OneSheet;

/**
 * Static class to build row strings. This is so much faster than
 * having row objects which in turn hold cell objects ... let alone
 * DOM/SimpleXml objects ... total performance and memory killer.
 *
 * Class RowHelper
 * @package OneSheet
 */
class RowHelper
{
    /**
     * Counter for the current row index,
     * increases by 1 for every added row.
     *
     * @var int
     */
    private static $rowIndex = 0;

    /**
     * Build XML String for a single data row and return it.
     *
     * @param array $dataRow
     * @param int $styleId
     * @return string
     */
    public static function buildRow(array $dataRow, $styleId = 0)
    {
        self::$rowIndex++;

        $cellXml = '';
        foreach (array_values($dataRow) as $cellNumber => $cellValue) {
            $cellXml .= CellHelper::buildCell(self::$rowIndex, $cellNumber, $cellValue, $styleId);
        }

        return '<row r="' . self::$rowIndex . '" spans="1:' . count($dataRow) . '">' . $cellXml . '</row>';
    }
}
