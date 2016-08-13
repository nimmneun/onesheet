<?php

namespace OneSheetTest;

use OneSheet\Style\Style;
use OneSheet\Style\Styler;

class StyleTest extends \PHPUnit_Framework_TestCase
{
    public function testFont()
    {
        $style = new Style();
        $style->setFontName('Arial')
            ->setFontSize(13)
            ->setFontColor('ffffff')
            ->setFontBold()
            ->setFontItalic()
            ->setFontUnderline()
            ->setFontStrikethrough();
        $expectedXml = '<font><sz val="13"/><color rgb="FFFFFF"/><name val="Arial"/><b/><i/><u/><s/></font>';
        $this->assertEquals($expectedXml, $style->getFont()->asXml());
    }

    public function testFill()
    {
        $style = new Style();
        $style->setFillColor('555555')
            ->setFillPattern('solid');
        $expectedXml = '<fill><patternFill patternType="solid"><fgColor rgb="555555"/></patternFill></fill>';
        $this->assertEquals($expectedXml, $style->getFill()->asXml());

        $style = new Style();
        $expectedXml = '<fill><patternFill patternType="none"/></fill>';
        $this->assertEquals($expectedXml, $style->getFill()->asXml());
    }

    public function testLock()
    {
        $styler = new Styler();
        $style = new Style();

        $unlockedFont = $style->getFont();
        $unlockedFill = $style->getFill();

        $styler->addStyle($style);

        $lockedFont = $style->getFont()->setColor('abcdef');
        $this->assertEquals($unlockedFont, $style->getFont());
        $this->assertNotEquals($unlockedFont, $lockedFont);

        $lockedFill = $style->setFillColor('abcdef');
        $this->assertEquals($unlockedFill, $style->getFill());
        $this->assertNotEquals($unlockedFill, $lockedFill);
    }
}
