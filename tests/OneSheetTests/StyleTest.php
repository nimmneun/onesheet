<?php
/**
 * @author neun
 * @since  2016-07-10
 */

namespace OneSheetTests;

use OneSheet\Style;

class StyleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFontXml()
    {
        $style = new Style();
        $style->size(15)
            ->color('ff9900')
            ->name('Arial')
            ->bold()
            ->italic()
            ->underline();
        $string = '<font><sz val="15"/><color rgb="FF9900"/><name val="Arial"/><b/><i/><u/></font>';
        $this->assertEquals($string, $style->getFontXml());
    }

    public function testGetFillXml()
    {
        $style = new Style();
        $style->fill('eeeeee');
        $string = '<fill><patternFill patternType="solid"><fgColor rgb="EEEEEE"/></patternFill></fill>';
        $this->assertEquals($string, $style->getFillXml());

        $style = new Style();
        $string = '<fill><patternFill patternType="none"/></fill>';
        $this->assertEquals($string, $style->getFillXml());
    }
}
