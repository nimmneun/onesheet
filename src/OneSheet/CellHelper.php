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
     * Control character map for escaping.
     *
     * @var array(array())
     */
    private static $ctrls = array();

    /**
     * Build and return the string for a single cell.
     *
     * @param int $rowId
     * @param int $cellNo
     * @param string $value
     * @param int|null $styleId
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function buildCell($rowId, $cellNo, $value, $styleId = 0)
    {
        if (is_numeric($value)) {
            return '<c r="' . self::buildId($cellNo, $rowId) . '" s="' . $styleId . '"><v>' . $value . '</v></c>';
        } elseif (ctype_alnum($value)) {
            return '<c r="' . self::buildId($cellNo, $rowId) . '" s="' . $styleId . '" t="inlineStr"><is><t>'
            . $value . '</t></is></c>';
        } elseif (ctype_print($value)) {
            return '<c r="' . self::buildId($cellNo, $rowId) . '" s="' . $styleId . '" t="inlineStr"><is><t>'
            . htmlspecialchars($value, ENT_QUOTES) . '</t></is></c>';
        } else {
            return '<c r="' . self::buildId($cellNo, $rowId) . '" s="' . $styleId . '" t="inlineStr"><is><t>'
            . str_replace(self::$ctrls['from'], self::$ctrls['to'], htmlspecialchars($value)) . '</t></is></c>';
        }
    }

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
     * Set mapping for control character escaping.
     *
     * @param array $map
     */
    public static function setCtrlCharacterMap(array $map)
    {
        self::$ctrls = $map;
    }
}
