<?php

namespace OneSheet\Xml;

class CellXml
{
    /**
     * XML constant for empty values.
     */
    const EMPTY_XML = '<c r="%s" s="%d"/>';

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
