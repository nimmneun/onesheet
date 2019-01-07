<?php

namespace OneSheet\Xml;

class RowXml
{
    /**
     * XML constant for default row strings.
     */
    const DEFAULT_XML = '<row r="%d" spans="1:%d">%s</row>';

    /**
     * XML constant for row strings with heights.
     */
    const HEIGHT_XML = '<row r="%d" spans="1:%d" ht="%d" customHeight="1">%s</row>';
}
