<?php

namespace OneSheetTest;

use OneSheet\Style\Style;
use OneSheet\Style\Styler;
use PHPUnit\Framework\TestCase;

class StylerTest extends TestCase
{
    public function testAddStyle()
    {
        $styler = new Styler();
        $initialXml = $styler->getStyleSheetXml();
        $styler->addStyle(new Style());

        self::assertEquals($initialXml, $styler->getStyleSheetXml());

        $style = new Style();
        $styler->addStyle($style->setFontBold());
        $oneNewStyleXml = $styler->getStyleSheetXml();
        self::assertNotEquals($initialXml, $oneNewStyleXml);

        $styler->addStyle($style->setFontBold());
        self::assertEquals($oneNewStyleXml, $styler->getStyleSheetXml());
    }
}
