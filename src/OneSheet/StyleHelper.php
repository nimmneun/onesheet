<?php
/**
 * @author neun
 * @since  2016-07-03
 */

namespace OneSheet;

/**
 * Class StyleHelper to build new style strings for style.xml.
 * @package Sheety
 */
class StyleHelper
{
    /**
     * Increase style index with every built style.
     * Start from 2, because 0 and 1 are reserved.
     *
     * @var int
     */
    private static $styleIndex = 1;

    /**
     * Concatenated <font> strings.
     *
     * @var string
     */
    private static $fontXml = '<font><sz val="11"/><color rgb="000000"/><name val="Calibri"/></font><font><sz val="11"/><color rgb="000000"/><name val="Calibri"/><b/></font>';

    /**
     * Concatenated <fill> strings
     *
     * @var string
     */
    private static $fillXml = '<fill><patternFill patternType="none"/></fill><fill><patternFill patternType="grey125"/></fill>';

    /**
     * Build a single new style based on given params.
     * Every newly built style will result in a completely new style set.
     *
     * @param Style $style
     * @return string
     */
    public static function buildStyle(Style $style)
    {
        self::$styleIndex++;
        self::$fontXml .= $style->getFontXml();
        self::$fillXml .= $style->getFillXml();

        return StyleHelper::$styleIndex;
    }

    /**
     * Return entire <fills> part for style.xml.
     *
     * @return string
     */
    public static function getFillsXml()
    {
        return '<fills count="' . (self::$styleIndex+1) . '">' . self::$fillXml . '</fills>';
    }

    /**
     * Return entire <fonts> part for style.xml.
     *
     * @return string
     */
    public static function getFontsXml()
    {
        return '<fonts count="' . (self::$styleIndex+1) . '">' . self::$fontXml . '</fonts>';
    }

    /**
     * Return entire cellXfs part for style.xml.
     *
     * @return string
     */
    public static function getCellXfsXml()
    {
        $xml = '<cellXfs count="' . (self::$styleIndex+1) . '">';

        for ($i = 0; $i <= self::$styleIndex; $i++) {
            $xml .= '<xf numFmtId="0" fontId="' . $i . '" fillId="' . $i . '" borderId="0" xfId="0" applyFont="1"/>';
        }

        return $xml . '</cellXfs>';
    }
}
