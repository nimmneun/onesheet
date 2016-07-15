<?php
/**
 * @author neun
 * @since  2016-07-11
 */

namespace OneSheet;

/**
 * Class DefaultRowBuilder to generate XML row strings.
 *
 * @package OneSheet
 */
class DefaultRowBuilder implements RowBuilderInterface
{
    /**
     * XML constant for row strings.
     */
    const ROW_XML = '<row r="%d" spans="1:%d">%s</row>';

    /**
     * @var CellBuilderInterface
     */
    private $cellBuilder;

    /**
     * Counter for the current row index,
     * increases by 1 for every added row.
     *
     * @var int
     */
    private $rowNumber = 1;

    /**
     * RowBuilder constructor.
     * @param CellBuilderInterface $cellBuilder
     */
    public function __construct(CellBuilderInterface $cellBuilder)
    {
        $this->cellBuilder = $cellBuilder;
    }

    /**
     * Build XML string for a single data row and return it.
     *
     * @param array $dataRow
     * @param int   $styleId
     * @return string
     */
    public function build(array $dataRow, $styleId = 0)
    {
        $cellXml = '';
        foreach (array_values($dataRow) as $cellNumber => $cellValue) {
            $cellXml .= $this->cellBuilder->build($this->rowNumber, $cellNumber, $cellValue, $styleId);
        }

        return sprintf(self::ROW_XML, $this->rowNumber++, count($dataRow), $cellXml);
    }
}
