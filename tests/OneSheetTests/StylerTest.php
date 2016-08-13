<?php
/**
 * @author neun
 * @since  2016-08-13
 */

namespace OneSheetTest;

use OneSheet\Style\Style;
use OneSheet\Style\Styler;

class StylerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddStyle()
    {
        $styler = new Styler();
        $initialXml = $styler->getStyleSheetXml();
        $styler->addStyle(new Style());

        $this->assertEquals($initialXml, $styler->getStyleSheetXml());

        $style = new Style();
        $styler->addStyle($style->setFontBold());
        $oneNewStyleXml = $styler->getStyleSheetXml();
        $this->assertNotEquals($initialXml, $oneNewStyleXml);

        $styler->addStyle($style->setFontBold());
        $this->assertEquals($oneNewStyleXml, $styler->getStyleSheetXml());
    }
}
