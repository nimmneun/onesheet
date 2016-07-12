<?php
/**
 * @author neun
 * @since  2016-07-12
 */

namespace OneSheet;

/**
 * Enforce a build method to generate row XML strings.
 *
 * @package OneSheet
 */
interface RowBuilderInterface
{
    /**
     * Build XML string for a single data row and return it.
     *
     * @param array $dataRow
     * @param int   $styleId
     * @return string
     */
    public function build(array $dataRow, $styleId = 0);
}
