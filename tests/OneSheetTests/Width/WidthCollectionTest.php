<?php

namespace OneSheetTest\Width;

use OneSheet\Width\WidthCollection;

class WidthCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $collection = new WidthCollection();
        $fontWidths = $collection->get('Arial', 15);

        $this->assertGreaterThanOrEqual(1.8, $fontWidths['A']);
        $this->assertLessThanOrEqual(2, $fontWidths['A']);
    }

    public function testGetNonExistantFontAndSize()
    {
        $collection = new WidthCollection();
        $fontWidths = $collection->get('Somefont', 25);
        $defaultFontWidths = $collection->get('Calibri', 11);

        $this->assertEquals($defaultFontWidths['A'], $fontWidths['A']);
    }
}
