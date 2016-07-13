<?php
/**
 * @author neun
 * @since  2016-07-11
 */

namespace OneSheet;

/**
 * Class DefaultCellBuilder to build xml cell strings.
 * Wheter a numeric value is written as a number or string type cell
 * is simply determined by type, to allow for some control by typecasting.
 * (Premature) performance considerations (50.000 rows x 15 columns):
 * - sprintf 8.3s
 * - '<c>' . $value . '</c>' 8.1s
 * - "<c>{$value}</c>" 7.9s
 * @package OneSheet
 */
class DefaultCellBuilder implements CellBuilderInterface
{
    /**
     * A-Z character array to build cell ids.
     *
     * @var array
     */
    private $characters = array();

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
     * CellBuilder constructor to instantiate character properties.
     */
    public function __construct()
    {
        $this->characters = range('A', 'Z');

        for ($i = 0; $i <= 255; $i++) {
            if (ctype_cntrl($char = chr($i))) {
                $this->controlCharacters[] = $char;
                $this->escapeCharacters[] = sprintf('_x%04s_', strtoupper(dechex($i)));
            }
        }
    }

    /**
     * Build and return the string for a single cell.
     *
     * @param int    $rowNumber
     * @param int    $cellNumber
     * @param string $value
     * @param int    $styleId
     * @return string
     */
    public function build($rowNumber, $cellNumber, $value, $styleId = 0)
    {
        $cellId = $this->getCellId($cellNumber, $rowNumber);

        if (is_int($value) || is_double($value)) {
            return sprintf(CellXml::NUMBER_XML, $cellId, $styleId, $value);
        } elseif (ctype_alnum($value) || is_numeric($value)) {
            return sprintf(CellXml::STRING_XML, $cellId, $styleId, $value);
        } elseif (is_bool($value)) {
            return sprintf(CellXml::BOOLEAN_XML, $cellId, $styleId, (int)$value);
        } elseif (ctype_print($value)) {
            return sprintf(CellXml::STRING_XML, $cellId, $styleId, htmlspecialchars($value, ENT_QUOTES));
        } elseif (0 == strlen($value)) {
            return '';
        } else {
            return sprintf(CellXml::STRING_XML, $cellId, $styleId, $this->escapeControlCharacters($value));
        }
    }

    /**
     * Turn a integer cell number + row number into a valid cell identifier
     * like e.g. A1, Z1, AA1 etc and return it.
     *
     * @param int      $cellNumber
     * @param int|null $rowNumber
     * @return string
     */
    private function getCellId($cellNumber, $rowNumber = null)
    {
        if ($cellNumber / 26 < 1) {
            return $this->characters[$cellNumber] . $rowNumber;
        }

        return $this->getCellId(floor($cellNumber / 26) - 1) . $this->characters[$cellNumber % 26] . $rowNumber;
    }

    /**
     * Replace control characters with escape characters.
     *
     * @param string $value
     * @return string
     */
    private function escapeControlCharacters($value)
    {
        return str_replace($this->controlCharacters, $this->escapeCharacters, htmlspecialchars($value, ENT_QUOTES));
    }
}
