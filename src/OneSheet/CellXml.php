<?php
/**
 * @author neun
 * @since  2016-07-13
 */

namespace OneSheet;

/**
 * Class CellXml serves as provider for cell xml strings.
 *
 * @package OneSheet
 */
class CellXml
{
    /**
     * XML constant for numeric values.
     */
    const NUMBER_XML = '<c r="%s" s="%d"><v>%s</v></c>';

    /**
     * XML constant for boolean values.
     */
    const BOOLEAN_XML = '<c r="%s" s="%d" t="b"><v>%d</v></c>';

    /**
     * XML constant for string values.
     */
    const STRING_XML = '<c r="%s" s="%d" t="inlineStr"><is><t>%s</t></is></c>';
}
