<?php
/**
 * @author neun
 * @since  2016-07-12
 */

namespace OneSheet;

/**
 * Enforce build method to generate cell XML strings.
 *
 * @package OneSheet
 */
interface CellBuilderInterface
{
    /**
     * Build and return the string for a single cell.
     *
     * @param int    $rowNumber
     * @param int    $cellNumber
     * @param string $value
     * @param int    $styleId
     * @return string
     */
    public function build($rowNumber, $cellNumber, $value, $styleId = 0);
}