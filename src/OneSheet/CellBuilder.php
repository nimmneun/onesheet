<?php

namespace OneSheet;

use OneSheet\Xml\CellXml;

/**
 * Class CellBuilder to build xml cell strings.
 * Whether a numeric value is written as a number or string type cell
 * is simply determined by type, to allow for some control by typecasting.
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
     * Regex pattern containing control characters.
     *
     * @var string
     */
    private $escapeCharacterPattern;

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

        $this->escapeCharacterPattern = '~[' . preg_quote(implode($this->controlCharacters)) . ']~';
    }

    /**
     * Build and return the string for a single cell.
     *
     * @param int   $rowNumber
     * @param int   $cellNumber
     * @param mixed $cellValue
     * @param int   $styleId
     *
     * @return string
     */
    public function build($rowNumber, $cellNumber, $cellValue, $styleId = 0)
    {
        $cellId = $this->getCellId($cellNumber, $rowNumber);

        if (is_int($cellValue) || is_float($cellValue)) {
            return sprintf(CellXml::NUMBER_XML, $cellId, $styleId, $cellValue);
        } elseif (is_bool($cellValue)) {
            return sprintf(CellXml::BOOLEAN_XML, $cellId, $styleId, (int)$cellValue);
        } elseif ($cellValue === null || 0 === strlen($cellValue)) {
            return 0 === $styleId ? '' : sprintf(CellXml::EMPTY_XML, $cellId, $styleId);
        }

        return sprintf(CellXml::STRING_XML, $cellId, $styleId, $this->escape($cellValue));
    }

    /**
     * Turn a integer cell number + row number into a valid cell identifier
     * like e.g. A1, Z1, AA1 etc and return it.
     *
     * @param int      $cellNumber
     * @param int|null $rowNumber
     *
     * @return string
     */
    public function getCellId($cellNumber, $rowNumber = null)
    {
        if ($cellNumber / 26 < 1) {
            return chr(65 + $cellNumber) . $rowNumber;
        }

        return $this->getCellId((int)($cellNumber / 26) - 1) . chr(65 + $cellNumber % 26) . $rowNumber;
    }

    /**
     * Escape/replace control characters.
     *
     * @param string $value
     *
     * @return string
     */
    private function escape($value)
    {
        if (1 !== preg_match($this->escapeCharacterPattern, $value)) {
            return htmlspecialchars($value, ENT_QUOTES);
        }

        return str_replace(
            $this->controlCharacters, $this->escapeCharacters, htmlspecialchars($value, ENT_QUOTES)
        );
    }
}

