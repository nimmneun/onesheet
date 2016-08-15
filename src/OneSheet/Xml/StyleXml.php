<?php

namespace OneSheet\Xml;

/**
 * Class StyleXml
 *
 * @package OneSheet
 */
class StyleXml
{
    /**
     * Constant for final fonts xml string containing all font strings.
     */
    const FONTS_XML = '<fonts count="%d">%s</fonts>';

    /**
     * Constant for final fills xml string.
     */
    const FILLS_XML = '<fills count="%d">%s</fills>';

    /**
     * Constant for final borders xml string.
     */
    const BORDERS_XML = '<borders count="%d">%s</borders>';
    /**
     * Constant for final cellXfs xml string.
     */
    const CELL_XFS_XML = '<cellXfs count="%d">%s</cellXfs>';

    /**
     * Constant for single font xml string.
     */
    const FONT_DEFAULT_XML = '<font><sz val="%d"/><color rgb="%s"/><name val="%s"/>%s%s%s%s</font>';

    /**
     * Constant for single solid colored fill (background) xml string.
     */
    const COLORED_FILL_XML = '<fill><patternFill patternType="solid"><fgColor rgb="%s"/></patternFill></fill>';

    /**
     * Constant for single uncolored fill (background) xml string.
     */
    const PATTERN_FILL_XML = '<fill><patternFill patternType="%s"/></fill>';

    /**
     * Constant for single border type element xml string.
     */
    const BORDER_TYPE_XML = '<%s style="%s"><color rgb="%s"/></%s>';

    /**
     * Constant for cellXf style xml strings.
     */
    const DEFAULT_XF_XML = '<xf numFmtId="0" fontId="%d" fillId="%d" borderId="%d"/>';

    /**
     * Constant for the full style xml.
     */
    const STYLE_SHEET_XML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">%s%s%s<cellStyleXfs count="1"><xf borderId="0" fillId="0" fontId="0" numFmtId="0"/></cellStyleXfs>%s<cellStyles count="1"><cellStyle builtinId="0" name="Normal" xfId="0"/></cellStyles><tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/></styleSheet>';
}
