<?php
/**
 * @author neun
 * @since  2016-07-03
 */

namespace OneSheet;

/**
 * Static class to build cell strings.
 *
 * Class CellHelper
 * @package OneSheet
 */
class CellHelper
{
    /**
     * Fixed character array to build cell ids, because chr(65+n)
     * eats to much performance.
     *
     * @var array
     */
    private static $chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

    /**
     * Turn a integer cell number + row number into a valid cell identifier
     * like e.g. A1, Z1, AA1 etc and return it.
     *
     * @param int $cellNo
     * @param int|null $rowIndex
     * @return string
     */
    private static function buildId($cellNo, $rowIndex = null)
    {
        if ($cellNo / 26 < 1) {
            return self::$chars[$cellNo] . $rowIndex;
        }

        return self::buildId(floor($cellNo / 26) - 1) . self::$chars[$cellNo % 26] . $rowIndex;
    }

    /**
     * Build and return the string for a single cell.
     *
     * @param int $rowId
     * @param int $cellNo
     * @param string $value
     * @param int|null $styleId
     * @return string
     */
    public static function buildString($rowId, $cellNo, $value, $styleId = 0)
    {
        if (is_int($value) || ctype_digit($value)) {
            return '<c r="' . self::buildId($cellNo, $rowId) . '" s="' . $styleId . '"><v>' . $value . '</v></c>';
        }

        if (!ctype_alnum($value)) {
            $value = self::escape($value);
        }

        return '<c r="' . self::buildId($cellNo, $rowId) . '" s="' . $styleId . '" t="inlineStr"><is><t>' . $value . '</t></is></c>';
    }

    /**
     * Escape string and return it.
     *
     * @param string $value
     * @return string
     */
    private static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }
}
