<?php
/**
 * @author neun
 * @since  2016-07-12
 */

namespace OneSheet;

interface StringCellInterface
{
    /**
     * XML constant for all other (string) values.
     */
    const STRING_XML = '<c r="%s" s="%d" t="inlineStr"><is><t>%s</t></is></c>';
}