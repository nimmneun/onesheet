<?php

namespace OneSheet\Xml;

/**
 * Class SheetXml
 *
 * @package OneSheet
 */
class SheetXml
{
    /**
     * Constant for sheet xml header string.
     */
    const HEADER_XML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" mc:Ignorable="x14ac" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac">';

    /**
     * Constant for single sheetview xml string.
     */
    const SHEETVIEWS_XML = '<sheetViews><sheetView tabSelected="1" workbookViewId="0" showGridLines="true" showRowColHeaders="1"><pane ySplit="%d" topLeftCell="%s" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>';

    /**
     * Constant for dimesion xml string.
     */
    const DIMENSION_XML = '<dimension ref="A1:%s"/>';

    /**
     * Constant for single column width string.
     */
    const COLUMN_XML = '<col min="%d" max="%d" width="%s" customWidth="1"/>';
}
