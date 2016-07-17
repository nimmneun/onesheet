<?php
/**
 * @author neun
 * @since  2016-07-17
 */

namespace OneSheet;

/**
 * Class SheetXml to provide default sheet strings.
 * @package OneSheet
 */
class SheetXml
{
    const HEADER_XML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xml:space="preserve" xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
    const SHEETVIEWS_XML = '<sheetViews><sheetView tabSelected="1" workbookViewId="0" showGridLines="true" showRowColHeaders="1"><pane ySplit="%d" topLeftCell="%s" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>';
    const DIMENSION_XML = '<dimension ref="A1:%s"/>';
}
