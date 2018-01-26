<?php

namespace OneSheetTest;

use OneSheet\Style\BorderStyle;
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
        $expectedXml = '<font><sz val="13"/><color rgb="FFFFFFFF"/><name val="Arial"/><b/><i/><u/><strike/></font>';
        self::assertEquals($expectedXml, $style->getFont()->asXml());
    }

    public function testFill()
    {
        $style = new Style();
        $style->setFillColor('555555')
            ->setFillPattern('solid');
        $expectedXml = '<fill><patternFill patternType="solid"><fgColor rgb="FF555555"/></patternFill></fill>';
        self::assertEquals($expectedXml, $style->getFill()->asXml());

        $style = new Style();
        $expectedXml = '<fill><patternFill patternType="none"/></fill>';
        self::assertEquals($expectedXml, $style->getFill()->asXml());
    }

    public function testNoBorderSet()
    {
        $style = new Style();
        $expectedXml = '<border/>';
        self::assertEquals($expectedXml, $style->getBorder()->asXml());
    }

    public function testBorder()
    {
        $style = new Style();
        $style->setBorderLeft(BorderStyle::DOUBLE, 'FF0000');
        $expectedXml = '<border><left style="double"><color rgb="FFFF0000"/></left><right/><top/><bottom/><diagonal/></border>';
        self::assertEquals($expectedXml, $style->getBorder()->asXml());

        $style = new Style();
        $style->setBorderRight(BorderStyle::THIN, 'FF0000')->setBorderTop(BorderStyle::DOUBLE, 'FF0000')->setBorderBottom(BorderStyle::DOUBLE, 'FF0000');
        $expectedXml = '<border><left/><right style="thin"><color rgb="FFFF0000"/></right><top style="double"><color rgb="FFFF0000"/></top><bottom style="double"><color rgb="FFFF0000"/></bottom><diagonal/></border>';
        self::assertEquals($expectedXml, $style->getBorder()->asXml());

        $style = new Style();
        $style->setBorderDiagonalUp(BorderStyle::DOUBLE, 'FF0000');
        $expectedXml = '<border diagonalUp="1"><left/><right/><top/><bottom/><diagonal style="double"><color rgb="FFFF0000"/></diagonal></border>';
        self::assertEquals($expectedXml, $style->getBorder()->asXml());

        $style = new Style();
        $style->setBorderDiagonalDown(BorderStyle::DOUBLE, 'FF0000');
        $expectedXml = '<border diagonalDown="1"><left/><right/><top/><bottom/><diagonal style="double"><color rgb="FFFF0000"/></diagonal></border>';
        self::assertEquals($expectedXml, $style->getBorder()->asXml());

        $style = new Style();
        $style->setSurroundingBorder();
        $expectedXml = '<border><left style="thin"><color rgb="FF000000"/></left><right style="thin"><color rgb="FF000000"/></right><top style="thin"><color rgb="FF000000"/></top><bottom style="thin"><color rgb="FF000000"/></bottom><diagonal/></border>';
        self::assertEquals($expectedXml, $style->getBorder()->asXml());
    }

    public function testLock()
    {
        $styler = new Styler();
        $style = new Style();

        $unlockedFont = $style->getFont();
        $unlockedFill = $style->getFill();
        $unlockedBorder = $style->getBorder();

        $styler->addStyle($style);

        $lockedFont = $style->getFont()->setColor('abcdef');
        self::assertEquals($unlockedFont, $style->getFont());
        self::assertNotEquals($unlockedFont, $lockedFont);

        $lockedFill = $style->setFillColor('abcdef');
        self::assertEquals($unlockedFill, $style->getFill());
        self::assertNotEquals($unlockedFill, $lockedFill);

        $lockedBorder = $style->setSurroundingBorder();
        self::assertEquals($unlockedBorder, $style->getBorder());
        self::assertNotEquals($unlockedBorder, $lockedBorder);
    }
}
