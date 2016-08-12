<?php

namespace OneSheet;

use OneSheet\Xml\CellXml;

/**
 * Class DefaultCellBuilder to build xml cell strings.
 * Wheter a numeric value is written as a number or string type cell
 * is simply determined by type, to allow for some control by typecasting.
 *
 * @package OneSheet
 */
class CellBuilder
{
    /**
     * Array of control characters that should be escaped.
     *
     * @var array
     */
    private $controlCharacters = array();

    /**
     * Array of escape characters to replace control characters.
     *
     * @var array
     */
    private $escapeCharacters = array();

    /**
     * CellBuilder constructor to create escaping arrays.
     */
    public function __construct()
    {
        foreach (range(chr(0), chr(31)) as $key => $character) {
            if (!in_array($character, array("\n", "\r", "\t"))) {
                $this->controlCharacters[] = $character;
                $this->escapeCharacters[] = sprintf('_x%04s_', strtoupper(dechex($key)));
            }
        }
    }

    /**
     * Build and return the string for a single cell.
     *
     * @param int   $rowNumber
     * @param int   $cellNumber
     * @param mixed $cellValue
     * @param int   $styleId
     * @return string
     */
    public function build($rowNumber, $cellNumber, $cellValue, $styleId = 0)
    {
        $cellId = $this->getCellId($cellNumber, $rowNumber);

        if (is_int($cellValue) || is_double($cellValue)) {
            return sprintf(CellXml::NUMBER_XML, $cellId, $styleId, $cellValue);
        } elseif (is_numeric($cellValue) || 1 != preg_match('~[^\w]~', $cellValue)) {
            return sprintf(CellXml::STRING_XML, $cellId, $styleId, htmlspecialchars($cellValue, ENT_QUOTES));
        } elseif (is_bool($cellValue)) {
            return sprintf(CellXml::BOOLEAN_XML, $cellId, $styleId, (int)$cellValue);
        }

        return sprintf(CellXml::STRING_XML, $cellId, $styleId, $this->escape($cellValue));
    }

    /**
     * Turn a integer cell number + row number into a valid cell identifier
     * like e.g. A1, Z1, AA1 etc and return it.
     *
     * @param int      $cellNumber
     * @param int|null $rowNumber
     * @return string
     */
    public function getCellId($cellNumber, $rowNumber = null)
    {
        if ($cellNumber / 26 < 1) {
            return chr(65 + $cellNumber) . $rowNumber;
        }

        return $this->getCellId(floor($cellNumber / 26) - 1) . chr(65 + $cellNumber % 26) . $rowNumber;
    }

    /**
     * Escape/replace control characters characters.
     *
     * @param string $value
     * @return string
     */
    private function escape($value)
    {
        return str_replace($this->controlCharacters, $this->escapeCharacters, htmlspecialchars($value, ENT_QUOTES));
    }
}

